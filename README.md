# Personal Finance — Transaction Management API

RESTful API для управления личными финансами, построенная на **Laravel 13**. Позволяет создавать, редактировать и удалять транзакции и категории.

---

## Возможности

- **Управление транзакциями** — полный CRUD (создание, просмотр, обновление, удаление)
- **Управление категориями** — полный CRUD с защитой от удаления категорий, привязанных к транзакциям
- **Типы транзакций** — `income` (доход) и `expense` (расход), реализованы через PHP 8.3 enum
- **Валидация** — Form Requests с строгой типизацией
- **Сервисный слой** — чистая архитектура с интерфейсами и зависимостями (Service/Contract pattern)
- **Базовые тесты** — Feature-тесты для транзакций


---

## Структура проекта

```
├── app/
│   ├── Contracts/              # Интерфейсы сервисов
│   │   ├── CategoryServiceInterface.php
│   │   └── TransactionServiceInterface.php
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
│   └── TransactionType.php     # Enum: income / expense
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
- `amount` — обязательно, число
- `type` — обязательно, `income` или `expense`
- `category_id` — обязательно, ID существующей категории
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
