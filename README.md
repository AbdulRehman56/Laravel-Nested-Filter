# Advanced Laravel Query Filter Package

A powerful and flexible filtering system for Laravel applications that supports complex database queries with nested relationships, logical operators, and dynamic filtering.

## 🚀 Features

- **Simple Filtering**: Basic column-based filtering with operators
- **Nested Relationships**: Filter through related models using dot notation
- **Logical Operators**: Support for AND, OR, and complex nested conditions
- **Relationship Filtering**: Filter records based on relationship existence
- **Dynamic Sorting**: Built-in sorting capabilities
- **Array Support**: Filter using arrays with 'IN' operator
- **Type Safety**: Proper validation and error handling

## 📦 Installation

```bash
composer require abdulrehman56/laravel-nested-filter
```

## 🔧 Basic Usage

### 1. Simple Column Filtering

Filter records based on a single column:

```php
// In your controller
$filters = [
    [
        'column_name' => 'name',
        'operator' => '=',
        'value' => 'John Doe'
    ]
];

$request->merge(['filters' => $filters]);
DynamicFilter::applyNestedWhereHas($request, $query);
```

**What this does**: Finds all records where the `name` column equals "John Doe"

### 2. Multiple Conditions with AND

Filter records that meet multiple conditions:

```php
$filters = [
    [
        'and' => [
            [
                'column_name' => 'age',
                'operator' => '>=',
                'value' => 18
            ],
            [
                'column_name' => 'status',
                'operator' => '=',
                'value' => 'active'
            ]
        ]
    ]
];
```

**What this does**: Finds records where age is 18 or older AND status is 'active'

### 3. Multiple Conditions with OR

Filter records that meet any of the conditions:

```php
$filters = [
    [
        'or' => [
            [
                'column_name' => 'status',
                'operator' => '=',
                'value' => 'active'
            ],
            [
                'column_name' => 'status',
                'operator' => '=',
                'value' => 'pending'
            ]
        ]
    ]
];
```

**What this does**: Finds records where status is either 'active' OR 'pending'

## 🔗 Relationship Filtering

### 1. Filter Through Related Models

Filter records based on related model data:

```php
$filters = [
    [
        'column_name' => 'department.name',
        'operator' => '=',
        'value' => 'Engineering'
    ]
];
```

**What this does**: Finds all records where the related department's name is 'Engineering'

### 2. Deep Nested Relationships

Filter through multiple levels of relationships:

```php
$filters = [
    [
        'column_name' => 'company.department.manager.name',
        'operator' => '=',
        'value' => 'Jane Smith'
    ]
];
```

**What this does**: Finds records where the company's department's manager's name is 'Jane Smith'

### 3. Complex Relationship Conditions

Combine multiple conditions within relationships:

```php
$filters = [
    [
        'column_name' => 'orders',
        'and' => [
            [
                'column_name' => 'orders.total',
                'operator' => '>=',
                'value' => 100
            ],
            [
                'column_name' => 'orders.status',
                'operator' => '=',
                'value' => 'completed'
            ]
        ]
    ]
];
```

**What this does**: Finds records that have orders with total >= 100 AND status = 'completed'

## 🔍 Advanced Filtering Options

### 1. Array-Based Filtering (IN Operator)

Filter records where a column value is in a list:

```php
$filters = [
    [
        'column_name' => 'category',
        'operator' => 'in',
        'value' => ['electronics', 'books', 'clothing']
    ]
];
```

**What this does**: Finds records where category is any of: electronics, books, or clothing

### 2. Relationship Existence Filtering

Filter records that have or don't have certain relationships:

```php
// Records that HAVE a specific relationship
$filters = [
    [
        'have' => 'orders'
    ]
];

// Records that DON'T HAVE a specific relationship
$filters = [
    [
        'does_not_have' => 'orders'
    ]
];
```

**What this does**: 
- First example: Finds records that have at least one order
- Second example: Finds records that have no orders

### 3. Complex Nested Logic

Combine multiple logical operations:

```php
$filters = [
    [
        'or' => [
            [
                'column_name' => 'status',
                'operator' => '=',
                'value' => 'active'
            ],
            [
                'and' => [
                    [
                        'column_name' => 'age',
                        'operator' => '>=',
                        'value' => 18
                    ],
                    [
                        'column_name' => 'verified',
                        'operator' => '=',
                        'value' => true
                    ]
                ]
            ]
        ]
    ]
];
```

**What this does**: Finds records where status is 'active' OR (age >= 18 AND verified = true)

## 📊 Sorting

Add sorting to your filtered results:

```php
$request->merge([
    'sort_by' => 'created_at',
    'sort_order' => 'desc'
]);

DynamicFilter::applyNestedWhereHas($request, $query);
```

**What this does**: Orders the results by created_at column in descending order

## 🛠️ Complete Controller Example

Here's how to use the filter system in a Laravel controller:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use AbdulRehman56\DynamicFilter\DynamicFilter;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Apply filters if provided
        if ($request->has('filters')) {
            DynamicFilter::applyNestedWhereHas($request, $query);
        }
        
        // Get paginated results
        $users = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
```

## 📝 API Request Examples

### Example 1: Simple Filter
```json
{
    "filters": [
        {
            "column_name": "email",
            "operator": "like",
            "value": "%@gmail.com"
        }
    ]
}
```

### Example 2: Relationship Filter
```json
{
    "filters": [
        {
            "column_name": "profile.city",
            "operator": "=",
            "value": "New York"
        }
    ],
    "sort_by": "created_at",
    "sort_order": "desc"
}
```

### Example 3: Complex Filter
```json
{
    "filters": [
        {
            "or": [
                {
                    "column_name": "status",
                    "operator": "=",
                    "value": "active"
                },
                {
                    "and": [
                        {
                            "column_name": "age",
                            "operator": ">=",
                            "value": 18
                        },
                        {
                            "column_name": "verified",
                            "operator": "=",
                            "value": true
                        }
                    ]
                }
            ]
        }
    ]
}
```

## 🔧 Supported Operators

- `=` - Equal to
- `!=` - Not equal to
- `>` - Greater than
- `>=` - Greater than or equal to
- `<` - Less than
- `<=` - Less than or equal to
- `like` - LIKE pattern matching
- `in` - Value in array
- `not in` - Value not in array
- `between` - Value between two values
- `null` - Column is null
- `not null` - Column is not null

## ⚠️ Important Notes

1. **Column Names**: Use dot notation for relationship columns (e.g., `department.name`)
2. **Case Sensitivity**: Operators are case-insensitive
3. **Validation**: Always validate your filter input before applying
4. **Performance**: Complex nested filters may impact performance on large datasets
5. **Relationships**: Ensure relationships are properly defined in your models

## 🐛 Troubleshooting

### Common Issues:

1. **"Column not found" error**: Check if the column exists in the table
2. **"Relationship not found" error**: Verify the relationship is defined in your model
3. **Unexpected results**: Check operator syntax and value types
4. **Performance issues**: Consider adding database indexes for frequently filtered columns

### Debug Mode:

Enable debug mode to see the generated SQL queries:

```php
// In your controller
DB::enableQueryLog();
DynamicFilter::applyNestedWhereHas($request, $query);
dd(DB::getQueryLog());
```

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

If you encounter any issues or have questions, please open an issue on GitHub.
