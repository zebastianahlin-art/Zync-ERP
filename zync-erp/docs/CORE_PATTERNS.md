# ZYNC ERP – Core Platform Patterns

This document describes the conventions used in the backend core so that new modules follow the same patterns.

---

## 1. Adding Routes

Routes are registered in `public/index.php` on the `$router` object before `$app->run()` is called.

```php
// Exact routes
$router->get('/', 'HomeController@index');
$router->post('/login', 'AuthController@login');

// Dynamic segments – use {param} syntax
$router->get('/customers/{id}',      'CustomerController@show');
$router->get('/customers/{id}/edit', 'CustomerController@edit');
$router->post('/customers/{id}',     'CustomerController@update');
```

Inside the handler / controller method, access the named segment via `$request->param()`:

```php
public function show(Request $request): Response
{
    $id = (int) $request->param('id');
    // …
}
```

**Rules:**
- Exact routes take priority over parameterised routes.
- `HEAD` requests are automatically handled using the matching `GET` route (RFC 7231).
- Unmatched routes return a `404 – Page Not Found` response.

---

## 2. Protecting Routes (Auth Guard)

Every controller that inherits from `App\Core\Controller` has access to `requireAuth()`.
Call it at the top of any protected action:

```php
public function edit(Request $request): Response
{
    if ($guard = $this->requireAuth()) {
        return $guard;   // redirects to /login with a flash error
    }

    // authenticated code below …
}
```

`requireAuth()` sets a `Flash::set('error', …)` message and returns a redirect Response to `/login`. The layout renders it automatically (see §5).

---

## 3. Using the Validator

`App\Core\Validator` provides chainable rules: `required`, `email`, `maxLength`.

```php
use App\Core\Validator;

public function store(Request $request): Response
{
    if ($guard = $this->requireAuth()) return $guard;

    $v = new Validator($request->body);
    $v->required('name', 'Name')
      ->required('email', 'Email')
      ->email('email', 'Email')
      ->maxLength('name', 100, 'Name');

    if ($v->fails()) {
        return $this->render('customers/create', [
            'title'  => 'New Customer',
            'errors' => $v->errors(),   // array<string, string>
        ]);
    }

    // validation passed – persist & redirect …
}
```

Displaying errors in a view:

```php
<?php if (!empty($errors)): ?>
    <ul class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
        <?php foreach ($errors as $field => $message): ?>
            <li><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
```

---

## 4. CSRF Protection for POST Forms

Every `POST` request is automatically verified by the Router.  
Include the hidden `_token` field in every HTML form:

```html
<form method="POST" action="/customers">
    <?= App\Core\Csrf::field() ?>
    <!-- … other fields … -->
</form>
```

`Csrf::field()` outputs:

```html
<input type="hidden" name="_token" value="<random-token>">
```

**What happens on failure:** the Router returns a `419 – CSRF Token Mismatch` response before the controller method is called.

**Notes:**
- Tokens are stored in the PHP session and regenerated on login (`Auth::login()`).
- `GET`/`HEAD` requests are never checked (they must remain idempotent).
- The token is auto-generated on the first call to `Csrf::token()` or `Csrf::field()`.

---

## 5. Flash Messages

`App\Core\Flash` persists one-time messages across a redirect (session-backed).

```php
// Set a message
Flash::set('success', 'Customer saved successfully.');
Flash::set('error',   'Something went wrong.');
return $this->redirect('/customers');
```

The main layout (`views/layouts/main.php`) automatically reads and displays both
`success` and `error` flash keys at the top of the main content area.

> **Note:** The login view reads the `error` flash itself via `Flash::get('error')` *inside the controller*
> and passes the result as an `$error` variable to the view template, which renders it inline within the card.
> By the time the layout runs, the flash has already been consumed from the session, so the layout shows
> nothing for that key. All other controllers (e.g. dashboard) rely on the layout's automatic flash rendering.

---

## Directory Reference

| Path | Purpose |
|------|---------|
| `app/Core/Router.php` | Route registration and dispatch |
| `app/Core/Request.php` | HTTP request wrapper (`params`, `input`, `param`) |
| `app/Core/Controller.php` | Base controller (`render`, `json`, `redirect`, `requireAuth`) |
| `app/Core/Validator.php` | Input validation utility |
| `app/Core/Csrf.php` | CSRF token generation and verification |
| `app/Core/Flash.php` | One-time flash messages |
| `app/Core/Auth.php` | Session-based authentication helper |
| `views/layouts/main.php` | Shared HTML layout (flash message rendering) |
| `public/index.php` | Application entry point and route registration |
