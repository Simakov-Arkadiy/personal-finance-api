# Personal Finance — Transaction Management API

RESTful API для управления личными финансами, построенная на **Laravel 13**. Позволяет создавать, редактировать и удалять транзакции и категории.

---

## Возможности

- **Управление транзакциями** — полный CRUD (создание, просмотр, обновление, удаление)
- **Управление категориями** — полный CRUD с защитой от удаления категорий, привязанных к транзакциям (HTTP 409 Conflict)
- **Типы транзакций** — `income` (доход) и `expense` (расход), реализованы через PHP 8.3 enum
- **Value Object `Money`** — суммы хранятся в минорных единицах (копейки), конвертация из/в десятичную строку
- **Поддержка валют** — RUB, USD, EUR, JPY с учётом разных масштабов (JPY — целое число)
- **Валидация** — Form Requests с строгой типизацией
- **Сервисный слой** — чистая архитектура с интерфейсами и DTO (Service/Contract pattern)
- **Feature-тесты** — 17 тестов, покрывающих CRUD, валидацию и граничные случаи

---

## Структура проекта

```
├── app/
│   ├── Contracts/              # Интерфейсы сервисов
│   │   ├── CategoryServiceInterface.php
│   │   └── TransactionServiceInterface.php
│   ├── DTO/                    # Data Transfer Objects
│   │   ├── CategoryDTO.php
│   │   └── TransactionDTO.php
│   ├── Http/
│   │   ├── Controllers/        # API-контроллеры
│   │   │   ├── CategoryController.php
│   │   │   └── TransactionController.php
│   │   └── Requests/           # Form Requests (валидация)
│   │       ├── StoreCategoryRequest.php
│   │       ├── UpdateCategoryRequest.php
│   │       ├── StoreTransactionRequest.php
│   │       └── UpdateTransactionRequest.php
│   ├── Models/                 # Eloquent-модели
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   └── User.php
│   ├── Providers/              # Service Provider (биндинг интерфейсов)
│   ├── Services/               # Бизнес-логика
│   │   ├── CategoryService.php
│   │   └── TransactionService.php
│   ├── Currency.php            # Enum: RUB, USD, EUR, JPY
│   ├── TransactionType.php     # Enum: income, expense
│   └── ValuesObject/
│       └── Money.php           # Value Object для сумм
├── database/
│   └── migrations/             # Миграции БД
├── routes/
│   └── api.php                 # API-маршруты
└── tests/
    └── Feature/
        └── TransactionTest.php # Feature-тесты
```

---

## API Reference

### Категории

| Метод    | Endpoint              | Описание                  |
|----------|-----------------------|---------------------------|
| `GET`    | `/api/categories`     | Получить все категории    |
| `GET`    | `/api/categories/{id}`| Получить категорию        |
| `POST`   | `/api/categories`     | Создать категорию         |
| `PUT`    | `/api/categories/{id}`| Обновить категорию        |
| `DELETE` | `/api/categories/{id}`| Удалить категорию         |

**Тело запроса (создание/обновление):**

```json
{
  "name": "Зарплата",
  "type": "income"
}
```

**Валидация:**
- `name` — обязательно, строка, до 100 символов, уникальна в рамках типа
- `type` — обязательно, `income` или `expense`

**Удаление:**
- Если категория привязана к транзакциям — возвращается **409 Conflict** с сообщением `Cannot delete category with associated transactions`
- Иначе — **200 OK** с сообщением `Category deleted successfully`

---

### Транзакции

| Метод    | Endpoint                  | Описание                  |
|----------|---------------------------|---------------------------|
| `GET`    | `/api/transactions`       | Получить все транзакции   |
| `GET`    | `/api/transactions/{id}`  | Получить транзакцию       |
| `POST`   | `/api/transactions`       | Создать транзакцию        |
| `PUT`    | `/api/transactions/{id}`  | Обновить транзакцию       |
| `DELETE` | `/api/transactions/{id}`  | Удалить транзакцию        |

**Тело запроса (создание/обновление):**

```json
{
  "amount": 5000.00,
  "type": "income",
  "category_id": 1,
  "description": "Оплата труда"
}
```

**Валидация:**
- `amount` — обязательно, число, строго больше нуля (`gt:0`)
- `type` — обязательно, `income` или `expense`
- `category_id` — обязательно, ID существующей категории, тип которой совпадает с `type` транзакции
- `description` — опционально, строка

---

## Схема базы данных

```
┌──────────────┐       ┌──────────────────┐
│   categories │       │   transactions   │
├──────────────┤       ├──────────────────┤
│ id       PK  │───────│ category_id  FK  │
│ name         │       │ id           PK  │
│ type         │       │ amount           │
│ timestamps   │       │ type             │
└──────────────┘       │ description      │
                       │ timestamps       │
                       └──────────────────┘
```

**Дополнительные таблицы Laravel:**
- `users` — пользователи
- `personal_access_tokens` — токены Sanctum
- `cache`, `cache_locks`, `sessions`, `jobs`, `job_batches`, `failed_jobs`

---

## Архитектурные решения

- **Service/Contract Pattern** — бизнес-логика вынесена в сервисы (`CategoryService`, `TransactionService`), контроллеры работают через интерфейсы
- **Dependency Injection** — интерфейсы биндятся в `AppServiceProvider`
- **DTO** — `TransactionDTO` и `CategoryDTO` инкапсулируют входные данные, `TransactionDTO` использует Value Object `Money` для суммы
- **Value Object `Money`** — суммы хранятся в минорных единицах (int), конвертация из десятичной строки через `Money::fromDecimalString()` и обратно через `toDecimalString()`
- **Enum для типов** — `TransactionType` гарантирует типобезопасность (`income` / `expense`)
- **Enum для валют** — `Currency` определяет код и масштаб (`getMinorMoneyUnitScale()`), по умолчанию используется `RUB`
- **Form Requests** — вся валидация вынесена в отдельные классы
- **Нормализация данных** — категории вынесены в отдельную таблицу с уникальным ограничением `[name, type]`

---

## Тестирование

```bash
# Запустить все тесты
composer run test

# Или через Artisan
php artisan test
```

---

## Разработка

```bash
# Запустить dev-сервер с hot-reload
composer run dev

# Запустить линтер Laravel Pint
./vendor/bin/pint

# Собрать фронтенд для продакшена
npm run build
```
