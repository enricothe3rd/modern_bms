# ✅ Status-Based Filtering for Obligation Requests

## Overview
Implemented filtering so users only see obligation requests that match their assigned review statuses, with the ability to toggle between filtered and all views.

## Features Implemented

### 1. Automatic Filtering by User's Assigned Statuses

**Default Behavior**: Users see only OBRs with statuses they're assigned to

**Example**:
- Budget Staff user assigned to "Budget Staff Review" → only sees OBRs with that status
- Budget Officer user assigned to "Budget Officer Final Review" → only sees OBRs with that status
- User with no status assignments → sees empty list (or all if toggled)

### 2. Toggle Button to Show All

**Filter Toggle**:
- Purple button in header: "Show All" / "Show My Statuses"
- Switches between filtered and unfiltered views
- Remembers preference via URL parameter

### 3. Visual Status Indicators

**Info Box** (when filtered):
- Purple info box showing which statuses are being filtered
- Displays color-coded badges for assigned statuses
- Explains that user can click "Show All" to see everything

**Header Text**:
- Shows current filter mode: "My Assigned Statuses Only" or "All Obligation Requests"

### 4. Restricted Status Changes

**Dropdown Restrictions**:
- Users can only change to statuses they're assigned to
- Other statuses appear as disabled with "(Not Assigned)" label
- Prevents unauthorized status changes

## How It Works

### Data Flow

```
User Login
  ↓
Get User's Department Assignments
  ↓
Extract Assigned Review Status IDs
  ↓
Filter OBRs by Status IDs (if filter enabled)
  ↓
Display Filtered Results
```

### Service Layer

**ObligationRequestService::getAllObligationRequests($filterByUserStatuses)**:
- If `$filterByUserStatuses = true`:
  - Gets user's assigned status IDs
  - Calls repository to filter by those IDs
  - Returns only matching OBRs
- If `$filterByUserStatuses = false`:
  - Returns all OBRs (no filtering)

### Repository Layer

**ObligationRequestRepository::getByReviewStatuses($statusIds)**:
```php
return ObligationRequest::with([...])
    ->whereIn('review_status_id', $statusIds)
    ->orderBy('created_at', 'desc')
    ->get();
```

### Controller Layer

**ObligationRequestController::index(Request $request)**:
- Reads `filter_by_status` parameter from URL
- Default: `my_statuses` (filtered)
- Passes filter flag to service
- Passes user's assigned status IDs to view

### View Layer

**Filter Toggle Button**:
```html
<form method="GET">
    <input type="hidden" name="filter_by_status" value="all|my_statuses">
    <button>Show All / Show My Statuses</button>
</form>
```

**Status Dropdown Restrictions**:
```php
@php
    $canChangeToStatus = empty($userAssignedStatusIds) || in_array($status->id, $userAssignedStatusIds);
@endphp
<option {{ !$canChangeToStatus ? 'disabled' : '' }}>
    {{ $status->name }}{{ !$canChangeToStatus ? ' (Not Assigned)' : '' }}
</option>
```

## User Experience

### Budget Staff User

**Assigned Statuses**: Budget Staff Review, Budget Staff Approved

**What They See**:
1. Page loads showing only OBRs with "Budget Staff Review" or "Budget Staff Approved"
2. Purple info box shows their assigned statuses
3. Header shows "My Assigned Statuses Only"
4. Status dropdown only allows changing to their assigned statuses
5. Can click "Show All" to see all OBRs (but still can't change to unassigned statuses)

### Budget Officer User

**Assigned Statuses**: Budget Officer Final Review, Final Approved

**What They See**:
1. Page loads showing only OBRs with "Budget Officer Final Review" or "Final Approved"
2. Purple info box shows their assigned statuses
3. Can only change status to their assigned statuses
4. Can toggle to see all OBRs

### Super Admin / User with No Assignments

**What They See**:
1. No filtering applied (sees all OBRs)
2. No info box shown
3. No toggle button (not needed)
4. Can change to any status

## URL Parameters

**Filter by User's Statuses** (default):
```
/obligation-requests?filter_by_status=my_statuses
```

**Show All**:
```
/obligation-requests?filter_by_status=all
```

## Benefits

✅ **Security**: Users can't see OBRs they shouldn't handle
✅ **Clarity**: Users only see relevant work
✅ **Flexibility**: Can toggle to see all if needed
✅ **Control**: Can't change to statuses they're not assigned to
✅ **Transparency**: Clear visual indicators of what's being filtered

## Files Modified

1. **app/Services/ObligationRequestService.php**
   - Added `$filterByUserStatuses` parameter to `getAllObligationRequests()`
   - Filters by user's assigned status IDs when enabled

2. **app/Repositories/ObligationRequestRepository.php**
   - Added `getByReviewStatuses($statusIds)` method
   - Filters OBRs using `whereIn('review_status_id', $statusIds)`
   - Added `reviewStatus` to eager loading

3. **app/Http/Controllers/ObligationRequestController.php**
   - Added `Request $request` parameter to `index()`
   - Reads `filter_by_status` from URL
   - Passes filter flag to service
   - Passes `$filterByStatus` to view

4. **resources/views/obligation-requests/index.blade.php**
   - Added filter toggle button in header
   - Added purple info box showing assigned statuses
   - Added status restrictions in dropdown (disabled unassigned statuses)
   - Added visual indicators for filter mode

## Testing Scenarios

### Scenario 1: Budget Staff User
1. Assign user to department with "Budget Staff Review" status
2. Create OBR with "Submitted" status
3. Create OBR with "Budget Staff Review" status
4. Login as Budget Staff user
5. Should see only the "Budget Staff Review" OBR
6. Click "Show All" → should see both OBRs
7. Try to change status → can only select assigned statuses

### Scenario 2: Budget Officer User
1. Assign user to department with "Budget Officer Final Review" status
2. Create OBR with "Budget Staff Approved" status
3. Create OBR with "Budget Officer Final Review" status
4. Login as Budget Officer user
5. Should see only the "Budget Officer Final Review" OBR
6. Toggle to see all → should see both
7. Status dropdown should disable unassigned statuses

### Scenario 3: User with Multiple Statuses
1. Assign user to department with multiple statuses
2. Should see OBRs matching any of their assigned statuses
3. Can change to any of their assigned statuses

## Summary

Users now have a personalized view of obligation requests based on their assigned review statuses. This ensures they only see and work on OBRs relevant to their role in the approval workflow, while still having the flexibility to view all OBRs when needed. The system prevents unauthorized status changes by disabling dropdown options for statuses the user isn't assigned to.
