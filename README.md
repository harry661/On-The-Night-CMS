# ON THE NIGHT CMS

A Filament 4 backend CMS for managing venues and users for the "ON THE NIGHT" React/Flutter app.

## Features

- **Role-based Access Control**: Admin and Venue Moderator roles
- **Venue Management**: Complete CRUD operations for venues with image uploads
- **User Management**: Manage users and their roles
- **Custom Branding**: ON THE NIGHT branding with primary color #C41E41
- **Media Management**: Image uploads with automatic conversions

## User Roles

### Admin
- Can view and edit all venues
- Can manage all users
- Full access to the system

### Venue Moderator
- Can only view and edit their assigned venue
- Limited access to user management (only their own profile)

## Installation

1. **Install PHP and Composer**
   ```bash
   # Install Homebrew (if not already installed)
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   
   # Install PHP and Composer
   brew install php composer
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   - Update `.env` with your database credentials
   - Create a MySQL database named `onthenight_cms`

5. **Run Migrations and Seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Start the Development Server**
   ```bash
   php artisan serve
   ```

## Default Login Credentials

After running the seeder, you can log in with:

**Admin User:**
- Email: `admin@onthenight.com`
- Password: `password`

**Venue Moderator:**
- Email: `moderator@onthenight.com`
- Password: `password`

## Project Structure

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── VenueResource.php
│   │   └── UserResource.php
│   └── Pages/
├── Models/
│   ├── User.php
│   └── Venue.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php

database/
├── migrations/
└── seeders/
    └── DatabaseSeeder.php
```

## Key Features

### Venue Management
- Complete venue information (name, description, address, contact details)
- Image uploads with automatic thumbnails
- Opening hours and amenities management
- Capacity and price range settings
- Active/featured status controls

### User Management
- Role-based permissions
- Venue assignment for moderators
- Secure password handling

### Customization
- ON THE NIGHT branding
- Primary color: #C41E41
- Custom navigation and layout

## API Integration

This CMS is designed to work with React/Flutter frontend applications. The venue data can be accessed via API endpoints for:
- Venue listings
- Venue details
- Popular venues
- Suggested venues

## Development

### Adding New Features
1. Create migrations for new database tables
2. Create models with relationships
3. Create Filament resources for admin interface
4. Update permissions and roles as needed

### Customizing the Theme
- Modify `app/Providers/Filament/AdminPanelProvider.php` for panel configuration
- Update colors, branding, and navigation as needed

## Security

- Role-based access control
- CSRF protection
- Secure file uploads
- Password hashing
- Session management

## Support

For issues or questions, please refer to the Filament documentation or create an issue in the project repository.

