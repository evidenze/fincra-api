# Technical Report: Wallet System Implementation

## Introduction

This report details the design and implementation decisions taken during the development of a basic wallet system. The system allows for secure crediting and debiting operations, ensuring protection against financial frauds and technical issues like race conditions and deadlocks. The backend is developed using Laravel PHP framework, and the frontend is implemented with a simple, functional UI. The database used is SQLite.

## Schema Design

### Database Schema

The database schema consists of the following tables:

1. **Users Table**:
    - `id`: Primary key.
    - `username`: String, the username of the user.
    - `email`: String, the email address of the user.
    - `password`: String, the hashed password of the user.
    - `is_admin`: Boolean, indicates if the user is an admin.
    - Timestamps for creation and updates.

2. **Transactions Table**:
    - `id`: Primary key.
    - `user_id`: Foreign key, references the `id` in the `users` table.
    - `type`: Enum, can be 'credit' or 'debit'.
    - `amount`: Decimal, the amount to be credited or debited.
    - Timestamps for creation and updates.

### Relationships

- A one-to-many relationship exists between `Users` and `Transactions`. Each user can have multiple transactions.

## Choice of Libraries and Frameworks

### Backend: Laravel PHP

- **Laravel**: Chosen for its robust MVC architecture, ease of use, and extensive ecosystem. Laravel provides built-in support for authentication, database migrations, and Eloquent ORM which simplifies database interactions.
- **Laravel Passport**: Used for API authentication via JWT. Passport offers a complete OAuth2 server implementation and easy integration with Laravel applications.
- **SQLite**: Selected for its simplicity and ease of setup for development purposes. SQLite is lightweight and suitable for small to medium-sized applications.


## Patterns Used for Safe Concurrency

### Database Transactions

To ensure safe concurrency and maintain data integrity, all credit and debit operations are performed within database transactions. This ensures that each operation is atomic and prevents issues such as partial updates or race conditions.

#### Implementation Example:

```php
use Illuminate\Support\Facades\DB;

public function credit(Request $request)
{
    $user = User::find(Auth::id());

    DB::transaction(function () use ($user, $request) {
        $user->lockForUpdate();
        $user->increment('balance', $request->amount);
        $user->transactions()->create(['type' => 'credit', 'amount' => $request->amount, 'user_id' => $request->user_id]);
    });

    return response()->json(['status' => true, 'message' => 'Amount credited successfully', 'data' => $user]);
}
```

### Optimistic Locking

Laravel's Eloquent ORM supports optimistic locking using the `version` column. This can be implemented to prevent race conditions by ensuring that updates are based on the latest data version.

### Pessimistic Locking

For critical operations, Laravel supports database-level locking mechanisms:

```php
DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
```

This locks the selected rows during the transaction, preventing other operations from modifying the data until the transaction is complete.

## Security Measures for Financial Transactions

### Authentication and Authorization

- **JWT Authentication**: Implemented using Laravel Passport to secure API endpoints. Only authenticated users can perform credit and debit operations.
- **Role-Based Access Control**: Admin functionalities are restricted to users with the `is_admin` flag set to true.

### Data Validation

All inputs are validated to ensure they meet the required criteria, such as valid amounts for transactions.

### Preventing Race Conditions and Deadlocks

- **Database Transactions**: Ensure atomicity and consistency.
- **Locking Mechanisms**: Use optimistic and pessimistic locking to manage concurrent access to resources.

### Logging and Monitoring

- All transactions are logged, providing an audit trail for monitoring and detecting any suspicious activities.

### Secure Data Storage

- **Password Hashing**: User passwords are hashed using bcrypt, ensuring they are not stored in plain text.
- **Environment Variables**: Sensitive information like database credentials and API keys are stored in environment variables and not hard-coded in the application.

## Conclusion

The wallet system was designed with a focus on security, robustness, and ease of use. The decisions taken during the design and implementation phases ensure the system can handle concurrent operations safely, prevent financial frauds, and maintain data integrity. Laravel's rich feature set and extensive ecosystem greatly facilitated the development process, providing built-in support for many required functionalities.