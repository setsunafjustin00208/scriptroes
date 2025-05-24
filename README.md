# Scriptores: CodeIgniter 4 + MongoDB Modern Starter

## Overview

Scriptores is a modern CodeIgniter 4 application starter, featuring:
- Grunt-powered asset pipeline (SCSS, JS/TS, Bulma, Font Awesome)
- MongoDB-based models and collections
- Modular user management with bitwise permissions and user types
- CLI and RESTful API for user and content management

## Features

- **Asset Pipeline**: Uses Grunt for compiling SCSS and JS/TS from `resources/`, outputs to `public/` with minification and watch support. Bulma and Font Awesome are integrated.
- **MongoDB Integration**: Models use MongoDB via a custom library. User data is split between `credentials` and `personal_information` collections, with references for profile data.
- **User Management**:
  - User types: super-admin, admin, user, publisher, editor, moderator, viewer
  - Bitwise permissions for fine-grained access control
  - CLI (`php spark user ...`) for create, update, delete, login, with interactive permission and profile prompts
  - REST API endpoints for register, login, logout, update, delete (see `UserController`)
- **Modern Frontend**: Bulma CSS, Font Awesome, Floating UI, Alpine.js, jQuery, SweetAlert2

## Installation & Setup

1. **Clone and Install**
   ```bash
   git clone <repo-url>
   cd scriptores
   composer install
   npm install
   ```
2. **Environment**
   - Copy `env` to `.env` and configure your `baseURL` and MongoDB connection string (see `.env` and `UserModel.php`).
3. **Asset Build with Grunt**
   - For development: `npm run dev`
   - For production: `npm run build`
   - Assets (SCSS, JS) are compiled/minified from `resources/` to `public/resources/`.
   - Bulma, Font Awesome, and Animate.css are integrated and copied to the correct public folders.
4. **Web Server**
   - Point your web server to the `public/` directory.

## MongoDB Collections

- `credentials`: Stores login, permissions, and references to personal info.
- `personal_information`: Stores user profile data (first name, last name, bio, avatar, etc.).

## User Management

### CLI Usage

- Create user:
  ```bash
  php spark user create
  ```
  - Prompts for username, password, email, user type, permissions, and personal info fields.
- Update user:
  ```bash
  php spark user update --id <user_id>
  ```
  - Prompts for update data and optionally personal info fields.
- Delete user:
  ```bash
  php spark user delete --id <user_id>
  ```
- Login (CLI):
  ```bash
  php spark user login
  ```

### REST API Endpoints

- Register: `POST /user/register` (JSON: username, password, email, type, permissions, personal_info)
- Login: `POST /user/login` (JSON: username, password)
- Logout: `POST /user/logout`
- Update: `PUT /user/update/{id}` (JSON: fields to update, including personal_info)
- Delete: `DELETE /user/delete/{id}`

## Development Process

- **Asset Development**: Edit files in `resources/js/` and `resources/scss/`. Use Grunt for live reload and builds (`npx grunt watch`).
- **Model/Controller Development**: Add or update models in `app/Models/` and controllers in `app/Controllers/`.
- **MongoDB Schema**: Collections are schemaless, but see `UserModel.php` for expected fields and structure.
- **Testing**: Use PHPUnit for backend tests. Place tests in `tests/`.
- **Extending Permissions**: Add new permission bits in `UserModel.php` and update CLI/API logic as needed.

## Example .env MongoDB Section

```
database.mongodb.connetion_string = mongodb://localhost:27017/?directConnection=true
```

## Contribution & Support

- Use GitHub issues for bugs and feature requests.
- Discuss on the [CodeIgniter forum](http://forum.codeigniter.com).

---

For more, see the user guide and code comments throughout the codebase.
