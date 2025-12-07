# ✅ Fixed: Review Status Assignment Not Saving

## Problem
When trying to assign review statuses to user department assignments, the statuses were not being saved to the database.

## Root Cause
The `review_status_ids` field was not included in the validation rules of `UserDepartmentAssignmentRequest`, causing Laravel to filter it out from the validated data before it reached the service layer.

## Solution

### 1. Updated UserDepartmentAssignmentRequest.php

**Added validation rules**:
```php
'review_status_ids' => 'nullable|array',
'review_status_ids.*' => 'exists:review_statuses,id'
```

**Added custom error messages**:
```php
'review_status_ids.array' => 'Review statuses must be an array.',
'review_status_ids.*.exists' => 'One or more selected review statuses do not exist.',
```

### 2. Added User Model Relationship

**Added to User.php**:
```php
public function departmentAssignments()
{
    return $this->hasMany(UserDepartmentAssignment::class);
}
```

This relationship is used in `ObligationRequestController` to get the user's assigned review statuses.

## How It Works Now

### Creating Assignment:
1. User selects department(s)
2. User checks review status checkboxes
3. Form submits with `review_status_ids[]` array
4. Request validates the array
5. Controller passes to service
6. Service creates assignment and syncs statuses via pivot table
7. Statuses saved successfully!

### Updating Assignment:
1. Edit button loads current statuses
2. User modifies status checkboxes
3. Form submits with updated `review_status_ids[]`
4. Request validates
5. Service updates assignment and syncs statuses
6. Pivot table updated with new statuses

## Data Flow

```
View Form
  ↓ (review_status_ids[] array)
UserDepartmentAssignmentRequest
  ↓ (validates and passes through)
UserDepartmentAssignmentController
  ↓ ($validated['review_status_ids'])
UserDepartmentAssignmentService
  ↓ (sync() method)
user_department_status_assignments table
  ✓ (statuses saved!)
```

## Testing

To verify it's working:

1. Go to Management → User Department Assignments
2. Click "Assign User to Departments"
3. Select a user and department
4. Check some review status checkboxes (e.g., "Budget Staff Review", "Budget Staff Approved")
5. Click "Assign"
6. Check the table - you should see colored status badges in the "Review Statuses" column
7. Click Edit on the assignment
8. The checkboxes should be pre-checked for the assigned statuses
9. Change the selections and update
10. Verify the badges update in the table

## Database Verification

Check the pivot table:
```sql
SELECT * FROM user_department_status_assignments;
```

You should see records with:
- `user_department_assignment_id`
- `review_status_id`
- `created_at`
- `updated_at`

## Files Modified

1. `app/Http/Requests/UserDepartmentAssignmentRequest.php`
   - Added review_status_ids validation rules
   - Added custom error messages

2. `app/Models/User.php`
   - Added departmentAssignments() relationship

## Summary

The issue was a simple validation oversight - the field wasn't allowed through validation, so it was being filtered out. Now that it's included in the validation rules, the review statuses save correctly to the pivot table and display properly in the UI.
