# Review Status Workflow System - Implementation Guide

## ✅ Completed:
1. Database migrations created and run
2. Models configured with relationships
3. Controller created

## 🔨 Next Steps:

### 1. Create Repository
**File:** `app/Repositories/ReviewStatusRepository.php`

```php
<?php

namespace App\Repositories;

use App\Models\ReviewStatus;

class ReviewStatusRepository
{
    public function all()
    {
        return ReviewStatus::ordered()->get();
    }

    public function active()
    {
        return ReviewStatus::active()->ordered()->get();
    }

    public function find($id)
    {
        return ReviewStatus::findOrFail($id);
    }

    public function create(array $data)
    {
        return ReviewStatus::create($data);
    }

    public function update($id, array $data)
    {
        $reviewStatus = $this->find($id);
        $reviewStatus->update($data);
        return $reviewStatus;
    }

    public function delete($id)
    {
        $reviewStatus = $this->find($id);
        return $reviewStatus->delete();
    }

    public function findByCode($code)
    {
        return ReviewStatus::where('code', $code)->first();
    }
}
```

### 2. Create Service
**File:** `app/Services/ReviewStatusService.php`

```php
<?php

namespace App\Services;

use App\Repositories\ReviewStatusRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewStatusService
{
    protected $repo;

    public function __construct(ReviewStatusRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllStatuses()
    {
        return $this->repo->all();
    }

    public function getActiveStatuses()
    {
        return $this->repo->active();
    }

    public function findStatus($id)
    {
        return $this->repo->find($id);
    }

    public function createStatus(array $data)
    {
        // Auto-generate code from name if not provided
        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['name'], '_');
        }

        return $this->repo->create($data);
    }

    public function updateStatus($id, array $data)
    {
        // Auto-generate code from name if not provided
        if (empty($data['code']) && !empty($data['name'])) {
            $data['code'] = Str::slug($data['name'], '_');
        }

        return $this->repo->update($id, $data);
    }

    public function deleteStatus($id)
    {
        return $this->repo->delete($id);
    }
}
```

### 3. Create Request
**File:** `app/Http/Requests/ReviewStatusRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('review_status');
        
        return [
            'name' => 'required|string|max:255|unique:review_statuses,name,' . $id,
            'code' => 'nullable|string|max:255|unique:review_statuses,code,' . $id,
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7', // Hex color
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Status name is required.',
            'name.unique' => 'This status name already exists.',
            'code.unique' => 'This status code already exists.',
            'color.required' => 'Please select a color.',
            'order.required' => 'Order is required.',
            'order.integer' => 'Order must be a number.',
        ];
    }
}
```

### 4. Update Controller
**File:** `app/Http/Controllers/ReviewStatusController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\ReviewStatusService;
use App\Http\Requests/ReviewStatusRequest;
use Illuminate\Http\Request;

class ReviewStatusController extends Controller
{
    protected $service;

    public function __construct(ReviewStatusService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $reviewStatuses = $this->service->getAllStatuses();
        return view('review-statuses.index', compact('reviewStatuses'));
    }

    public function store(ReviewStatusRequest $request)
    {
        $this->service->createStatus($request->validated());
        return redirect()->route('review-statuses.index')
            ->with('success', 'Review status created successfully!');
    }

    public function edit($id)
    {
        $reviewStatus = $this->service->findStatus($id);
        $reviewStatuses = $this->service->getAllStatuses();
        return view('review-statuses.index', compact('reviewStatus', 'reviewStatuses'));
    }

    public function update(ReviewStatusRequest $request, $id)
    {
        $this->service->updateStatus($id, $request->validated());
        return redirect()->route('review-statuses.index')
            ->with('success', 'Review status updated successfully!');
    }

    public function destroy($id)
    {
        $this->service->deleteStatus($id);
        return redirect()->route('review-statuses.index')
            ->with('success', 'Review status deleted successfully!');
    }
}
```

### 5. Add Routes
**File:** `routes/web.php`

Add this line:
```php
Route::resource('review-statuses', ReviewStatusController::class)->middleware('auth');
```

### 6. Create Seeder
**File:** `database/seeders/ReviewStatusSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReviewStatus;

class ReviewStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Draft',
                'code' => 'draft',
                'description' => 'Initial draft status',
                'color' => '#6B7280',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Submitted',
                'code' => 'submitted',
                'description' => 'Submitted for review',
                'color' => '#3B82F6',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Under Review',
                'code' => 'under_review',
                'description' => 'Currently being reviewed',
                'color' => '#F59E0B',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Approved',
                'code' => 'approved',
                'description' => 'Approved by reviewer',
                'color' => '#10B981',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Rejected',
                'code' => 'rejected',
                'description' => 'Rejected by reviewer',
                'color' => '#EF4444',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Final Approval',
                'code' => 'final_approval',
                'description' => 'Final approval stage',
                'color' => '#8B5CF6',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            ReviewStatus::create($status);
        }
    }
}
```

Run: `php artisan db:seed --class=ReviewStatusSeeder`

### 7. Create View (Similar to other CRUD views)
**File:** `resources/views/review-statuses/index.blade.php`

Copy structure from `resources/views/sectors/index.blade.php` and modify for review statuses.

Key fields to display:
- Name
- Code
- Description
- Color (show color badge)
- Order
- Status (Active/Inactive)
- Actions (Edit/Delete)

### 8. Update Sidebar
**File:** `config/sidebar.php`

Add under Management section:
```php
[
    'name' => 'Review Statuses',
    'route' => 'review-statuses.index',
    'icon' => 'check-circle',
],
```

## Next Phase: Integrate with User Department Assignments

Once Review Status CRUD is complete, update User Department Assignment to allow selecting multiple statuses when assigning users to departments.

This enables the workflow where users can only handle specific approval stages in their assigned departments!


---

## ✅ COMPLETED STEPS (December 7, 2025)

### Phase 1: Review Status CRUD - COMPLETE ✓

1. ✅ Fixed syntax error in `ReviewStatusController.php` (changed `/` to `\` in use statement)
2. ✅ Created `resources/views/review-statuses/index.blade.php` with full CRUD interface
   - DataTables integration with export buttons
   - Color picker for status colors
   - Order management
   - Active/Inactive toggle
   - Modal-based add/edit forms
   - Delete confirmation
3. ✅ Route already exists via `Route::resource('review-statuses', ReviewStatusController::class)`
4. ✅ Added to Management Dashboard in `ManagementController.php`
   - Module name: "Review Statuses"
   - Icon: check-circle (violet color)
   - Permission: manage_departments
5. ✅ Added check-circle icon SVG to `management/index.blade.php`
6. ✅ Seeder already ran (6 default statuses created)

### What's Working:
- Full CRUD operations for Review Statuses
- Accessible from Management Dashboard
- Color-coded status display
- Order-based sorting
- Active/Inactive status management
- Auto-generated codes from names

### Next Steps (Phase 2):
- [ ] Update User Department Assignment view to include multi-select status checkboxes
- [ ] Update `UserDepartmentAssignmentController` to handle status assignments
- [ ] Update `UserDepartmentAssignmentService` to sync status relationships
- [ ] Add status badges to User Department Assignment table
- [ ] Test workflow: Assign user to department with specific statuses
