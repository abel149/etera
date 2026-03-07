# 🧪 Laravel Project Testing Guide

This guide provides comprehensive testing instructions for all endpoints and functionality in your Laravel project.

## 📋 Table of Contents

1. [Quick Start](#quick-start)
2. [Test Files Overview](#test-files-overview)
3. [Running Tests](#running-tests)
4. [Test Categories](#test-categories)
5. [Troubleshooting](#troubleshooting)
6. [Adding New Tests](#adding-new-tests)

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+ installed
- Composer installed
- Laravel project set up
- Database configured

### Quick Test Run
```bash
# Run all tests
php artisan test

# Run specific test class
php artisan test --filter=SimpleEndpointTest

# Run with verbose output
php artisan test -v
```

### Using Test Scripts
```bash
# Windows
run-tests.bat

# PowerShell
.\run-tests.ps1
```

## 📁 Test Files Overview

### 1. `SimpleEndpointTest.php`
- **Purpose**: Basic functionality testing without complex dependencies
- **Use Case**: Quick validation of core endpoints
- **Dependencies**: Minimal - only basic User model

### 2. `EndpointTesting.php`
- **Purpose**: Comprehensive testing of all endpoints
- **Use Case**: Full project validation
- **Dependencies**: All models, factories, and services

## 🏃‍♂️ Running Tests

### Method 1: Command Line
```bash
# Navigate to project directory
cd /path/to/your/project

# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/SimpleEndpointTest.php

# Run specific test method
php artisan test --filter=test_user_can_login_with_email

# Run tests with coverage (if configured)
php artisan test --coverage
```

### Method 2: Using Test Scripts
```bash
# Windows Batch File
run-tests.bat

# PowerShell Script
.\run-tests.ps1
```

### Method 3: Individual Test Categories
```bash
# Authentication tests only
php artisan test --filter=test_guest_can_access_login_page

# Admin functionality tests
php artisan test --filter=test_admin_can_access_dashboard

# Role-based access tests
php artisan test --filter=test_unauthorized_user_cannot_access_admin_routes
```

## 🧪 Test Categories

### 1. Authentication Endpoints
- Login page access
- User authentication (email/phone)
- Logout functionality
- Unapproved user restrictions
- CSRF protection

### 2. Role-Based Access Control
- Admin dashboard access
- Insurance dashboard access
- Garage dashboard access
- Shop dashboard access
- Business owner dashboard access
- Marketer dashboard access
- Employee dashboard access
- Individual dashboard access

### 3. Admin Functionality
- User approval system
- User management
- Garage management
- Shop management
- Business owner management
- Marketer management

### 4. Insurance Functionality
- Proforma access
- Partner management
- Garage partnerships
- Shop partnerships

### 5. Garage Functionality
- Proforma access
- Received proforma details
- Application submission

### 6. Shop Functionality
- Proforma access
- Received proforma details
- Parts management

### 7. Business Owner Functionality
- Proforma creation
- Proforma management
- Dashboard access

### 8. Marketer Functionality
- User registration
- Dashboard access
- Relationship management

### 9. File Management
- File uploads
- File deletion
- Media handling

### 10. Proforma System
- Creation
- Applications
- Closing
- Status management

### 11. Security & Validation
- SQL injection protection
- XSS protection
- Input validation
- Middleware protection

### 12. Performance & Error Handling
- Dashboard load times
- 404 error handling
- 500 error handling
- Database integrity

## 🔧 Troubleshooting

### Common Issues

#### 1. Database Connection Errors
```bash
# Check database configuration
php artisan config:clear
php artisan config:cache

# Ensure test database exists
php artisan migrate --env=testing
```

#### 2. Factory Errors
```bash
# Clear cached factories
php artisan config:clear

# Regenerate autoload files
composer dump-autoload
```

#### 3. Permission Errors
```bash
# Check storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

#### 4. Test Timeout Issues
```bash
# Increase test timeout in phpunit.xml
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="QUEUE_DRIVER" value="sync"/>
    <env name="MAIL_DRIVER" value="array"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="HASH_COST" value="4"/>
</php>
```

### Debug Mode
```bash
# Run tests with detailed output
php artisan test -v

# Run single test with debug
php artisan test --filter=test_name --verbose
```

## ➕ Adding New Tests

### 1. Create New Test File
```bash
php artisan make:test NewFeatureTest
```

### 2. Test Structure Template
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_feature()
{
    // Arrange
        $user = User::factory()->create();
    
    // Act
        $response = $this->actingAs($user)->get('/new-feature');
    
    // Assert
    $response->assertStatus(200);
    }
}
```

### 3. Test Naming Conventions
- Use descriptive names: `test_user_can_create_proforma`
- Group related tests in same class
- Use consistent naming patterns

### 4. Test Data Setup
```php
protected function setUp(): void
{
    parent::setUp();
    
    // Create test data
    $this->user = User::factory()->create([
        'role' => 'admin',
        'approved' => true,
    ]);
}
```

## 📊 Test Results Interpretation

### Success Indicators
- ✅ All tests pass
- ✅ Green checkmarks
- ✅ No errors or failures

### Warning Signs
- ⚠️ Slow test execution (>3 seconds per test)
- ⚠️ Database connection issues
- ⚠️ Factory creation failures

### Failure Analysis
- 🔴 Check error messages
- 🔴 Verify test data setup
- 🔴 Check model relationships
- 🔴 Validate route definitions

## 🎯 Best Practices

### 1. Test Organization
- Group related tests together
- Use descriptive test names
- Keep tests independent
- Clean up after each test

### 2. Test Data Management
- Use factories for consistent data
- Avoid hardcoded values
- Clean up test data properly
- Use database transactions when possible

### 3. Performance Considerations
- Keep tests fast (<3 seconds each)
- Use in-memory databases for testing
- Mock external services
- Avoid unnecessary database queries

### 4. Security Testing
- Test authentication requirements
- Validate role-based access
- Check CSRF protection
- Test input validation

## 📈 Continuous Integration

### GitHub Actions Example
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php artisan test
```

## 🆘 Getting Help

### Resources
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Best Practices](https://laravel.com/docs/testing#testing-essentials)

### Support
- Check Laravel logs: `storage/logs/laravel.log`
- Review test output for specific error messages
- Verify database configuration
- Check model relationships and factories

---

**Happy Testing! 🎉**

Remember: Good tests lead to reliable code, and reliable code leads to happy users!
