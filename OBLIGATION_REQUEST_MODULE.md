# Obligation Request (OBR) Module

## Overview
Complete module for creating and managing Obligation Requests with cascading dropdowns, multiple line items, and signatory management.

## Database Structure

### Tables Created
1. **obligation_requests** - Main OBR table
   - obr_number (unique)
   - department_id (Responsibility Center)
   - claimant_payee_id
   - obligation_date
   - particulars
   - optional_field_1, optional_field_2
   - total_amount (auto-calculated)
   - status (draft, submitted, approved, rejected)

2. **obligation_request_signatories** - Signatories
   - obligation_request_id
   - user_id
   - signatory_type (signatory_1, signatory_2, noted)
   - signatory_date
   - order

3. **obligation_request_items** - Line items
   - obligation_request_id
   - fund_type_id
   - department_id
   - expense_type_id
   - account_id
   - sub_account_id (optional)
   - amount
   - order

## Features

### Main Form Fields
- **OBR Number** - Unique identifier
- **Responsibility Center** - Department selection
- **Claimant Payee** - From claimant_payees table
- **Obligation Date** - Date picker
- **Particulars** - Text area for details
- **Optional Fields** - 2 additional text fields

### Signatories
- **Signatory 1** - Required with optional date
- **Signatory 2** - Required with optional date
- **Noted By** - Required with optional date

### Line Items (Multiple Entries)
Each item has cascading dropdowns:
1. **Fund Type** → Loads departments
2. **Department** → Loads expense types for that department
3. **Expense Type** → Selected
4. **Account** → Loads all accounts
5. **Sub Account** → Loads sub-accounts for selected account (optional)
6. **Amount** - Decimal input

### Cascading Logic
- Fund Type selection → Fetches departments with expense types
- Department selection → Fetches expense types allocated to that department
- Account selection → Fetches sub-accounts for that account
- Total amount auto-calculated from all line items

## Files Created

### Migrations
- `2025_12_06_035442_create_obligation_requests_table.php`
- `2025_12_06_035455_create_obligation_request_signatories_table.php`
- `2025_12_06_035508_create_obligation_request_items_table.php`

### Models
- `app/Models/ObligationRequest.php`
- `app/Models/ObligationRequestSignatory.php`
- `app/Models/ObligationRequestItem.php`

### Repositories
- `app/Repositories/ObligationRequestRepository.php`
- `app/Repositories/ClaimantPayeeRepository.php`
- `app/Repositories/FundTypeRepository.php`

### Services
- `app/Services/ObligationRequestService.php`

### Controllers
- `app/Http/Controllers/ObligationRequestController.php`
- `app/Http/Controllers/Api/ObligationRequestApiController.php`

### Requests
- `app/Http/Requests/ObligationRequestRequest.php`

### Views
- `resources/views/obligation-requests/index.blade.php`
- `resources/views/obligation-requests/show.blade.php`

### Routes
- Resource routes for CRUD operations
- API routes for cascading dropdowns:
  - `/api/obligation-requests/fund-types/{fundType}/departments`
  - `/api/obligation-requests/fund-types/{fundType}/departments/{department}/expense-types`
  - `/api/obligation-requests/accounts`
  - `/api/obligation-requests/accounts/{account}/sub-accounts`

### Configuration
- Added "Obligation Requests" menu item to sidebar

## Usage

### Creating an OBR
1. Click "+ Create OBR" button
2. Fill in OBR details (number, responsibility center, claimant payee, date, particulars)
3. Select 3 signatories with optional dates
4. Add optional fields if needed
5. Add line items:
   - Click "+ Add Item"
   - Select fund type (loads departments)
   - Select department (loads expense types)
   - Select expense type
   - Select account (loads sub-accounts)
   - Optionally select sub-account
   - Enter amount
6. Add more items as needed
7. Click "Create" to save

### Viewing an OBR
- Click the eye icon to view full details
- Shows all information including signatories and line items
- Displays total amount

### Editing an OBR
- Click the edit icon
- Modify any fields
- Items and signatories are replaced on update

### Deleting an OBR
- Click the delete icon
- Confirm deletion
- Cascades to signatories and items

## API Endpoints

### Web Routes
- `GET /obligation-requests` - List all OBRs
- `POST /obligation-requests` - Create new OBR
- `GET /obligation-requests/{id}` - View OBR details
- `GET /obligation-requests/{id}/edit` - Edit form
- `PUT /obligation-requests/{id}` - Update OBR
- `DELETE /obligation-requests/{id}` - Delete OBR

### API Routes (for AJAX)
- `GET /api/obligation-requests/fund-types/{fundType}/departments`
- `GET /api/obligation-requests/fund-types/{fundType}/departments/{department}/expense-types`
- `GET /api/obligation-requests/accounts`
- `GET /api/obligation-requests/accounts/{account}/sub-accounts`

## Service-Repository Pattern
Follows the established pattern:
- **Controller** - Handles HTTP requests/responses
- **Service** - Contains business logic, transactions
- **Repository** - Data access layer
- **Request** - Validation rules

## Notes
- Total amount is automatically calculated from line items
- All relationships use cascade delete
- Supports JSON responses for API usage
- DataTables integration for listing
- Modal-based form for create/edit
- Dynamic item rows with remove functionality
- At least one line item is required
