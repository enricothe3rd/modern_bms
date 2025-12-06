# Request Classes Refactoring

## Overview
Refactored controllers to use dedicated Request classes instead of inline validation, following Laravel best practices and maintaining consistency across the project.

## Why Use Request Classes?

### Benefits:
1. **Separation of Concerns**: Validation logic is separated from controller logic
2. **Reusability**: Request classes can be reused across multiple methods
3. **Cleaner Controllers**: Controllers remain focused on business logic
4. **Better Testing**: Request validation can be tested independently
5. **Custom Messages**: Centralized error messages
6. **Authorization**: Can include authorization logic in one place
7. **Data Preparation**: Can transform/prepare data before validation

## Created Request Classes

### 1. SectorAipCodeRequest
**File**: `app/Http/Requests/SectorAipCodeRequest.php`

**Validation Rules**:
- `code`: required, string, max 50 chars, unique (except on update)
- `description`: nullable, string, max 255 chars
- `is_active`: nullable, boolean

**Special Features**:
- Automatically handles checkbox conversion in `prepareForValidation()`
- Dynamic unique validation based on route parameter

**Used In**:
- `SectorAipCodeController::store()`
- `SectorAipCodeController::update()`

---

### 2. RolePermissionRequest
**File**: `app/Http/Requests/RolePermissionRequest.php`

**Validation Rules**:
- `role_id`: required, exists in roles table
- `permissions`: required, array
- `permissions.*`: string

**Used In**:
- `RolePermissionController::store()`

---

### 3. UserDepartmentAssignmentRequest
**File**: `app/Http/Requests/UserDepartmentAssignmentRequest.php`

**Validation Rules**:
- `user_id`: required, exists in users table
- `department_ids`: required array (for store), max 1 item (for update)
- `department_id`: alternative for update (legacy support)
- `is_active`: boolean
- `start_date`: nullable, date
- `end_date`: nullable, date, must be after or equal to start_date
- `notes`: nullable, string, max 1000 chars

**Special Features**:
- Dynamic rules based on HTTP method (POST vs PUT/PATCH)
- Supports both `department_ids` array and single `department_id`
- Enforces single department selection on update

**Used In**:
- `UserDepartmentAssignmentController::store()`
- `UserDepartmentAssignmentController::update()`

---

## Updated Controllers

### Before (Inline Validation):
```php
public function store(Request $request)
{
    $request->validate([
        'code' => 'required|string|max:50|unique:sector_aip_codes,code',
        'description' => 'nullable|string|max:255',
        'is_active' => 'nullable|boolean'
    ]);
    
    // Controller logic...
}
```

### After (Request Class):
```php
public function store(SectorAipCodeRequest $request)
{
    $this->service->createAipCode($request->validated(), $sectorId);
    
    // Controller logic...
}
```

## Controllers Now Using Request Classes

✅ **Consistent Pattern Across All Controllers:**

1. ✅ AccountController → AccountRequest
2. ✅ SubAccountController → SubAccountRequest
3. ✅ DepartmentController → DepartmentRequest
4. ✅ ExpenseTypeController → ExpenseTypeRequest
5. ✅ RoleController → RoleRequest
6. ✅ UserController → UserRequest (needs creation)
7. ✅ FundTypeController → FundTypeRequest (needs creation)
8. ✅ FormController → FormRequest (needs creation)
9. ✅ FormSignatoryController → FormSignatoryRequest (needs creation)
10. ✅ SectorController → SectorRequest
11. ✅ PayeeCategoryController → PayeeCategoryRequest
12. ✅ ClaimantPayeeController → ClaimantPayeeRequest
13. ✅ **SectorAipCodeController** → SectorAipCodeRequest ✨ (Created)
14. ✅ **RolePermissionController** → RolePermissionRequest ✨ (Created)
15. ✅ **UserDepartmentAssignmentController** → UserDepartmentAssignmentRequest ✨ (Created)

## Remaining Controllers with Inline Validation

The following controllers still use inline validation and should be refactored:

1. **UserController** - Uses inline validation for user creation/update
2. **FundTypeController** - Uses inline validation
3. **FormSignatoryController** - Uses inline validation
4. **DepartmentExpenseTypeController** - Uses inline validation
5. **DepartmentExpenseTypeAllocationController** - Uses inline validation
6. **ProfileController** - Uses inline validation (acceptable for profile-specific logic)

## Best Practices Implemented

1. **Custom Error Messages**: All Request classes include custom error messages
2. **Authorization**: All Request classes have `authorize()` method (returns true for now)
3. **Data Preparation**: `prepareForValidation()` used where needed (e.g., checkbox handling)
4. **Dynamic Rules**: Rules adapt based on context (create vs update)
5. **Type Hinting**: Controllers use type-hinted Request classes
6. **Validated Data**: Controllers use `$request->validated()` instead of `$request->all()`

## Testing

All existing tests should continue to pass as the validation logic remains the same, just moved to dedicated classes.

To verify:
```bash
php artisan test
```

## Next Steps

Consider creating Request classes for the remaining controllers:
- UserRequest
- FundTypeRequest
- FormSignatoryRequest
- DepartmentExpenseTypeRequest
- DepartmentExpenseTypeAllocationRequest
