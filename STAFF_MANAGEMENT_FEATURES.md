# Staff Management Module - Detailed Feature Specification

## Overview

This document provides a comprehensive breakdown of all functionality that will be added to the catering management system as part of the Staff Management Module implementation.

---

## Table of Contents

1. [Staff Directory Management](#1-staff-directory-management)
2. [Staff Assignment to Events](#2-staff-assignment-to-events)
3. [Attendance Tracking](#3-attendance-tracking)
4. [Staff Reports & Analytics](#4-staff-reports--analytics)
5. [Integration Features](#5-integration-features)
6. [User Interface Details](#6-user-interface-details)

---

## 1. Staff Directory Management

### 1.1 Staff List Page (`/staff`)

**Purpose**: View and manage all staff members in the system

**Features**:
- **Staff Table Display**:
  - Staff Name
  - Phone Number (clickable to call)
  - Email Address (clickable to email)
  - Staff Role (Cook, Waiter, Manager, Helper, etc.)
  - Status Badge (Active/Inactive with color coding)
  - Total Events Assigned (count of events this staff member has worked)
  - Last Assignment Date
  - Actions Column (View, Edit, Delete buttons)

- **Search & Filter Functionality**:
  - **Search Bar**: Search by staff name, phone, or email (real-time search)
  - **Role Filter**: Dropdown to filter by staff role (All, Cook, Waiter, Manager, Helper, etc.)
  - **Status Filter**: Dropdown to filter by status (All, Active, Inactive)
  - **Sort Options**: 
    - Sort by Name (A-Z, Z-A)
    - Sort by Role
    - Sort by Total Events (Most/Least)
    - Sort by Last Assignment Date

- **Pagination**: 
  - 15 staff members per page (configurable)
  - Page navigation controls

- **Quick Actions**:
  - "Add New Staff" button (top right)
  - Bulk actions (select multiple staff for bulk operations - future enhancement)
  - Export to Excel button (export staff list)

- **Visual Elements**:
  - Status badges (Green for Active, Gray for Inactive)
  - Role badges with different colors
  - Responsive table design (mobile-friendly)

**Permissions Required**: `staff.view` or `staff`

---

### 1.2 Create Staff Page (`/staff/create`)

**Purpose**: Add a new staff member to the system

**Form Fields**:
- **Name** (Required)
  - Text input
  - Validation: Required, max 255 characters
  - Placeholder: "Enter full name"

- **Phone Number** (Required)
  - Text input with phone format validation
  - Validation: Required, unique per tenant, valid phone format
  - Placeholder: "+1234567890"
  - Help text: "Include country code"

- **Email Address** (Optional)
  - Email input
  - Validation: Valid email format, unique per tenant if provided
  - Placeholder: "staff@example.com"

- **Staff Role** (Required)
  - Dropdown/Select field
  - Options: Cook, Waiter, Manager, Helper, Driver, Supervisor, etc.
  - Can be predefined list or custom entry
  - Validation: Required

- **Address** (Optional)
  - Textarea
  - Validation: Max 1000 characters
  - Placeholder: "Enter full address"

- **Status** (Required)
  - Radio buttons or dropdown
  - Options: Active, Inactive
  - Default: Active
  - Validation: Required

**Form Features**:
- Real-time validation feedback
- Cancel button (returns to staff list)
- Submit button (creates staff and redirects to staff detail page)
- Success message on creation
- Error messages for validation failures

**Permissions Required**: `staff.create`

---

### 1.3 Edit Staff Page (`/staff/{id}/edit`)

**Purpose**: Update existing staff member information

**Features**:
- Same form fields as Create page
- Pre-filled with existing staff data
- All fields are editable
- Phone and email uniqueness validation (excluding current staff member)
- Update button saves changes
- Cancel button returns to staff detail page
- Success message on update

**Permissions Required**: `staff.edit`

---

### 1.4 Staff Detail Page (`/staff/{id}`)

**Purpose**: View comprehensive information about a staff member

**Page Sections**:

#### Section 1: Staff Information Card
- **Profile Section**:
  - Staff Name (large heading)
  - Staff Role (badge)
  - Status Badge (Active/Inactive)
  - Phone Number (with click-to-call link)
  - Email Address (with click-to-email link)
  - Address (if provided)

- **Quick Stats Cards**:
  - Total Events Worked (number)
  - Upcoming Events (count)
  - Attendance Rate (percentage)
  - Average Events Per Month (number)

- **Action Buttons**:
  - Edit Staff
  - Assign to Event
  - Mark Attendance
  - View Workload Report
  - View Performance Report
  - Delete Staff (with confirmation)

#### Section 2: Tabs Navigation
- **Tab 1: Upcoming Events**
  - Table showing all future events this staff is assigned to
  - Columns: Event Date, Event Time, Order Number, Customer Name, Role in Event, Status
  - Sortable columns
  - Click on order number to view order details
  - Empty state: "No upcoming events"

- **Tab 2: Past Events**
  - Table showing all completed events
  - Same columns as Upcoming Events
  - Pagination for large lists
  - Filter by date range
  - Export to Excel option

- **Tab 3: Attendance History**
  - Calendar view or table view toggle
  - Table columns: Date, Status (Present/Absent/Late/Half Day), Check-in Time, Check-out Time, Notes
  - Calendar view shows attendance status for each day
  - Filter by date range
  - Statistics: Total Present Days, Total Absent Days, Attendance Rate

- **Tab 4: Performance Metrics**
  - Charts and graphs:
    - Events worked per month (line chart)
    - Attendance trend (line chart)
    - Role distribution (pie chart - if staff worked in different roles)
  - Key metrics:
    - Total Events: X
    - Average Events Per Month: Y
    - Attendance Rate: Z%
    - Punctuality Score: A%
    - Most Common Role: B

#### Section 3: Notes Section (Optional)
- Internal notes about the staff member
- Add/edit notes functionality
- Notes history with timestamps

**Permissions Required**: `staff.view` or `staff`

---

### 1.5 Delete Staff Functionality

**Purpose**: Remove staff member from system

**Features**:
- Delete button in staff list and detail page
- Confirmation modal before deletion
- Validation: Cannot delete if staff has active event assignments
- Soft delete option (mark as inactive instead of hard delete)
- Success message after deletion
- Redirect to staff list after deletion

**Permissions Required**: `staff.delete`

---

## 2. Staff Assignment to Events

### 2.1 Assign Staff to Event Page (`/orders/{orderId}/assign-staff`)

**Purpose**: Assign one or more staff members to a specific event/order

**Page Layout**:

#### Section 1: Event Information
- Order Number
- Customer Name
- Event Date
- Event Time
- Event Address
- Guest Count
- Order Status

#### Section 2: Currently Assigned Staff
- Table showing staff already assigned to this event
- Columns: Staff Name, Role, Assigned Date, Actions
- Remove button for each assigned staff
- Empty state if no staff assigned

#### Section 3: Assign New Staff
- **Available Staff List**:
  - Filter by Role (Cook, Waiter, etc.)
  - Search by name
  - Shows only available staff (not assigned to conflicting events on same date/time)
  - Checkbox selection for multiple staff
  - Staff information: Name, Role, Phone, Status

- **Assignment Details** (for each selected staff):
  - Role for this event (dropdown - can differ from staff's default role)
  - Notes (optional textarea)
  - Checkbox: "Notify staff" (if notification system is enabled)

- **Bulk Assignment**:
  - Quick assign buttons: "Assign 2 Cooks", "Assign 3 Waiters", etc.
  - Auto-selects available staff based on role

#### Section 4: Staff Availability Check
- Visual indicator showing:
  - Available staff count by role
  - Conflicting events warning (if staff is already assigned to another event)
  - Recommended staff count based on guest count

**Form Features**:
- Real-time availability checking
- Validation: At least one staff must be selected
- Validation: Role must be specified for each staff
- Save button assigns staff and redirects to order details
- Cancel button returns to order details

**Permissions Required**: `staff.create`

---

### 2.2 Staff Assignment in Order Details

**Purpose**: View and manage staff assignments directly from order page

**Features**:
- New section in order details page: "Assigned Staff"
- Table showing all staff assigned to this order
- Columns: Staff Name, Role, Phone, Status, Actions
- "Assign Staff" button (links to assign-staff page)
- Remove staff button (with confirmation)
- Edit assignment button (change role or notes)
- Staff count summary: "2 Cooks, 3 Waiters assigned"

**Integration**: This section appears in `/orders/{id}` page

---

### 2.3 Staff Assignment Validation

**Business Rules**:
- Staff cannot be assigned to two events on the same date and time
- System checks for conflicts before allowing assignment
- Warning message if staff is assigned to another event on the same day (different time)
- Error message if staff is inactive
- Validation: Staff must exist and belong to same tenant

---

## 3. Attendance Tracking

### 3.1 Attendance List Page (`/attendance`)

**Purpose**: View all attendance records

**Features**:
- **Attendance Table**:
  - Date
  - Staff Name (clickable to staff profile)
  - Status (Present/Absent/Late/Half Day) with color badges
  - Check-in Time
  - Check-out Time
  - Working Hours (calculated)
  - Notes
  - Actions (Edit, Delete)

- **Filters**:
  - Date Range Picker (from/to dates)
  - Staff Filter (dropdown - select specific staff)
  - Status Filter (All, Present, Absent, Late, Half Day)
  - Role Filter (filter by staff role)

- **Search**: Search by staff name

- **Sort Options**:
  - Sort by Date (newest/oldest)
  - Sort by Staff Name
  - Sort by Status

- **Quick Actions**:
  - "Mark Attendance" button (individual)
  - "Bulk Mark Attendance" button (for all staff on a date)
  - Export to Excel

- **Statistics Cards** (above table):
  - Total Present Today
  - Total Absent Today
  - Attendance Rate (this month)
  - Late Arrivals (this month)

**Permissions Required**: `attendance.view` or `attendance`

---

### 3.2 Mark Individual Attendance (`/attendance/create`)

**Purpose**: Record attendance for a single staff member

**Form Fields**:
- **Staff** (Required)
  - Dropdown/Select with search
  - Shows active staff only
  - Validation: Required

- **Date** (Required)
  - Date picker
  - Default: Today's date
  - Validation: Required, cannot be future date (configurable)

- **Status** (Required)
  - Radio buttons or dropdown
  - Options:
    - Present (default)
    - Absent
    - Late
    - Half Day
  - Validation: Required

- **Check-in Time** (Optional, shown if Present/Late/Half Day)
  - Time picker
  - Default: Current time (if marking for today)
  - Validation: Required if status is Present/Late/Half Day

- **Check-out Time** (Optional)
  - Time picker
  - Validation: Must be after check-in time if both provided

- **Notes** (Optional)
  - Textarea
  - Placeholder: "Add any notes about this attendance"
  - Max 500 characters

**Form Features**:
- Real-time validation
- Warning if attendance already exists for this staff on this date
- Option to update existing record
- Save button creates attendance record
- Cancel button returns to attendance list

**Permissions Required**: `attendance.create`

---

### 3.3 Bulk Mark Attendance (`/attendance/bulk`)

**Purpose**: Mark attendance for all staff on a specific date

**Features**:
- **Date Selection**:
  - Date picker
  - Default: Today
  - Cannot select future dates

- **Staff List Table**:
  - Shows all active staff members
  - Columns:
    - Checkbox (select staff)
    - Staff Name
    - Role
    - Status (dropdown for each staff)
    - Check-in Time (time picker)
    - Check-out Time (time picker)
    - Notes (text input)

- **Quick Actions**:
  - "Mark All as Present" button
  - "Mark All as Absent" button
  - "Mark Selected as Present" button
  - "Mark Selected as Absent" button

- **Existing Attendance Indicator**:
  - Shows if attendance already exists for a staff member on selected date
  - Option to update existing records
  - Visual indicator (different row color)

- **Bulk Save**:
  - Saves attendance for all selected staff
  - Shows success count and error count
  - Lists any errors (e.g., validation failures)

**Permissions Required**: `attendance.create`

---

### 3.4 Edit Attendance (`/attendance/{id}/edit`)

**Purpose**: Update existing attendance record

**Features**:
- Same form as create page
- Pre-filled with existing data
- All fields editable
- Update button saves changes
- Delete button (with confirmation)
- Success message on update

**Permissions Required**: `attendance.edit`

---

### 3.5 Attendance Calendar View

**Purpose**: Visual calendar showing attendance for all staff

**Features**:
- Monthly calendar view
- Color coding:
  - Green: Present
  - Red: Absent
  - Yellow: Late
  - Orange: Half Day
  - Gray: No record
- Click on date to view/edit attendance
- Filter by staff member
- Navigation: Previous/Next month
- Legend showing color meanings
- Export calendar view as PDF

**Location**: Can be accessed from attendance list page or staff detail page

---

### 3.6 Staff Attendance History (`/attendance/staff/{staffId}`)

**Purpose**: View attendance history for a specific staff member

**Features**:
- Staff information header
- Attendance table (same as main attendance list but filtered)
- Statistics:
  - Total Days Worked
  - Total Days Absent
  - Attendance Rate
  - Average Check-in Time
  - Average Check-out Time
- Charts:
  - Attendance trend (line chart)
  - Status distribution (pie chart)
- Filter by date range
- Export to Excel

**Permissions Required**: `attendance.view`

---

## 4. Staff Reports & Analytics

### 4.1 Staff Workload Report (`/staff/{id}/workload`)

**Purpose**: Analyze staff workload and availability

**Features**:
- **Date Range Selector**: Filter by date range (default: current month)

- **Summary Statistics**:
  - Total Events Assigned (in date range)
  - Average Events Per Week
  - Busiest Week
  - Most Common Role
  - Total Working Days

- **Charts**:
  - Events per week (bar chart)
  - Events per month (line chart)
  - Role distribution (pie chart)
  - Workload trend (line chart showing events over time)

- **Detailed Table**:
  - All events in date range
  - Columns: Date, Event Time, Order Number, Customer, Role, Status
  - Sortable and filterable

- **Availability Analysis**:
  - Available dates (no events assigned)
  - Overloaded dates (multiple events)
  - Recommended rest days

- **Export Options**:
  - Export to Excel
  - Export to PDF
  - Print report

**Permissions Required**: `staff.view`

---

### 4.2 Staff Performance Report (`/staff/{id}/performance`)

**Purpose**: Evaluate staff performance metrics

**Features**:
- **Performance Metrics**:
  - Total Events Worked: X
  - Attendance Rate: Y%
  - Punctuality Score: Z% (based on check-in times)
  - Average Events Per Month: A
  - Customer Satisfaction: B (if feedback system integrated)

- **Charts**:
  - Performance trend over time (line chart)
  - Attendance vs Events correlation (scatter chart)
  - Monthly performance comparison (bar chart)

- **Performance Breakdown**:
  - By Role: Performance in each role
  - By Month: Monthly performance metrics
  - By Event Type: Performance by order type

- **Comparisons**:
  - Compare with team average
  - Compare with previous period
  - Ranking among all staff

- **Recommendations**:
  - Areas of improvement
  - Strengths
  - Suggested training (if applicable)

- **Export Options**: Excel, PDF, Print

**Permissions Required**: `staff.view`

---

### 4.3 Attendance Report (`/attendance/report`)

**Purpose**: Comprehensive attendance analytics

**Features**:
- **Date Range Selector**: Filter by date range

- **Summary Statistics**:
  - Total Staff: X
  - Average Attendance Rate: Y%
  - Total Present Days: A
  - Total Absent Days: B
  - Total Late Arrivals: C
  - Most Punctual Staff: [Name]
  - Least Punctual Staff: [Name]

- **Charts**:
  - Daily attendance rate (line chart)
  - Attendance by status (pie chart)
  - Attendance by role (bar chart)
  - Monthly attendance trend (line chart)
  - Staff-wise attendance comparison (bar chart)

- **Staff-wise Breakdown**:
  - Table showing each staff member's attendance stats
  - Columns: Name, Role, Present Days, Absent Days, Late Days, Attendance Rate
  - Sortable columns
  - Click staff name to view detailed history

- **Date-wise Breakdown**:
  - Table showing attendance for each date
  - Columns: Date, Present Count, Absent Count, Late Count, Attendance Rate
  - Click date to view detailed records

- **Export Options**: Excel, PDF, Print

**Permissions Required**: `attendance.view`

---

### 4.4 Staff Utilization Report (Future Enhancement)

**Purpose**: Analyze staff resource utilization

**Features**:
- Staff capacity vs actual usage
- Underutilized staff identification
- Overutilized staff identification
- Optimal staff allocation recommendations
- Cost per event analysis (if salary data integrated)

---

## 5. Integration Features

### 5.1 Dashboard Integration

**New Widgets Added to Dashboard**:

#### Widget 1: Staff Availability Today
- Shows count of available staff today
- Shows count of staff assigned to events today
- Quick link to assign staff

#### Widget 2: Upcoming Staff Assignments
- List of next 5 events requiring staff
- Shows assigned staff count vs required
- Link to assign staff if needed

#### Widget 3: Today's Attendance
- Count of staff marked present today
- Count of staff marked absent today
- Attendance rate for today
- Link to mark attendance

#### Widget 4: Staff Performance Summary
- Top performing staff (by events worked)
- Staff with best attendance rate
- Staff needing attention (low attendance)

---

### 5.2 Order Module Integration

**Enhanced Order Details Page**:

- **New Section: Assigned Staff**
  - Table showing all staff assigned to this order
  - Quick actions: Assign, Remove, Edit
  - Staff count summary

- **New Button: "Assign Staff"**
  - Appears in order actions
  - Links to staff assignment page
  - Shows badge if staff already assigned

- **Staff Requirements Suggestion**:
  - Based on guest count, suggest staff requirements
  - Example: "For 100 guests, recommend 2 cooks, 4 waiters"

---

### 5.3 Reports Module Integration

**New Reports Added to Reports Menu**:

- **Staff Workload Report** (in Reports section)
  - Overall staff workload analysis
  - All staff comparison
  - Workload distribution charts

- **Staff Performance Report** (in Reports section)
  - Team performance overview
  - Individual staff comparisons
  - Performance trends

- **Attendance Summary Report** (in Reports section)
  - Monthly/Yearly attendance summaries
  - Attendance trends
  - Compliance reports

---

### 5.4 Notification Integration (If Notification System Exists)

**Automated Notifications**:

- **Staff Assignment Notification**:
  - Notify staff when assigned to an event
  - Email/SMS with event details

- **Attendance Reminder**:
  - Remind to mark attendance daily
  - Sent to managers/admins

- **Low Attendance Alert**:
  - Alert if staff attendance drops below threshold
  - Sent to managers

---

## 6. User Interface Details

### 6.1 Navigation Menu

**New Menu Items**:

- **Staff Management** (Main Menu)
  - Staff List
  - Add Staff
  - Attendance
  - Staff Reports

- **Staff** (Sub-menu under Staff Management)
  - All Staff
  - Add New Staff

- **Attendance** (Sub-menu under Staff Management)
  - Mark Attendance
  - Attendance List
  - Attendance Report
  - Attendance Calendar

---

### 6.2 Permission-Based Access

**Permission Structure**:

- `staff` - Full access to staff module
- `staff.view` - View staff only
- `staff.create` - Create staff and assign to events
- `staff.edit` - Edit staff information
- `staff.delete` - Delete staff
- `attendance` - Full access to attendance
- `attendance.view` - View attendance only
- `attendance.create` - Mark attendance
- `attendance.edit` - Edit attendance

**Access Control**:
- Users without permissions cannot see menu items
- Unauthorized access shows 403 error
- Buttons/actions hidden based on permissions

---

### 6.3 Responsive Design

**Mobile Optimization**:
- Tables become cards on mobile
- Filters collapse into accordion
- Touch-friendly buttons
- Swipe actions on mobile
- Responsive charts

**Tablet Optimization**:
- Optimized table layouts
- Side-by-side forms where possible
- Touch-friendly interface

---

### 6.4 Data Export Features

**Export Options Available**:

1. **Staff List Export**:
   - Excel format
   - Columns: Name, Phone, Email, Role, Status, Total Events
   - Filename: `staff-list-{date}.xlsx`

2. **Attendance Export**:
   - Excel format
   - Columns: Date, Staff Name, Status, Check-in, Check-out, Notes
   - Filename: `attendance-{date-range}.xlsx`

3. **Staff Report Export**:
   - Excel and PDF formats
   - Includes charts and statistics
   - Filename: `staff-report-{staff-name}-{date}.xlsx/pdf`

---

### 6.5 Search & Filter UI

**Search Features**:
- Real-time search (as you type)
- Search across multiple fields
- Search suggestions/autocomplete
- Clear search button
- Search history (optional)

**Filter Features**:
- Collapsible filter panel
- Multiple filter combinations
- Filter presets (save common filters)
- Clear all filters button
- Active filter indicators (badges)

---

## 7. Technical Features

### 7.1 Data Validation

**Staff Validation**:
- Phone number format validation
- Email format validation
- Unique phone per tenant
- Unique email per tenant (if provided)
- Required field validation
- Character limit validation

**Assignment Validation**:
- Staff availability check
- Conflict detection (same date/time)
- Role validation
- Order existence validation

**Attendance Validation**:
- Date validation (no future dates)
- Time validation (check-out after check-in)
- Duplicate attendance prevention
- Staff existence validation

---

### 7.2 Performance Optimizations

- **Lazy Loading**: Load relationships only when needed
- **Pagination**: All lists paginated (15 items per page)
- **Caching**: Cache frequently accessed data
- **Database Indexing**: Indexes on tenant_id, staff_id, date fields
- **Query Optimization**: Efficient queries with proper joins

---

### 7.3 Error Handling

- **User-Friendly Error Messages**: Clear, actionable error messages
- **Validation Errors**: Inline validation with field-specific errors
- **404 Handling**: Proper 404 pages for missing resources
- **403 Handling**: Proper authorization error pages
- **500 Handling**: Graceful error handling with logging

---

### 7.4 Audit Trail

- **Activity Logging**: Log all staff-related actions
- **Change Tracking**: Track who created/updated staff
- **History**: Maintain history of changes (if soft deletes implemented)

---

## 8. Future Enhancement Possibilities

### 8.1 Advanced Features (Not in Initial Implementation)

- **Staff Scheduling**: Create weekly/monthly schedules
- **Shift Management**: Define shifts and assign staff
- **Leave Management**: Track staff leaves and holidays
- **Payroll Integration**: Link attendance to payroll
- **Staff Ratings**: Customer feedback on staff performance
- **Skills Management**: Track staff skills and certifications
- **Training Records**: Track staff training and certifications
- **Staff Photos**: Upload and display staff photos
- **Mobile App**: Mobile app for staff to mark their own attendance
- **GPS Tracking**: Track staff location during events (privacy considerations)

---

## Summary

The Staff Management Module will add comprehensive functionality for:

1. ✅ **Managing Staff Directory** - CRUD operations for staff
2. ✅ **Assigning Staff to Events** - Link staff to orders/events
3. ✅ **Tracking Attendance** - Record and monitor staff attendance
4. ✅ **Generating Reports** - Workload, performance, and attendance reports
5. ✅ **Dashboard Integration** - Staff widgets on main dashboard
6. ✅ **Order Integration** - Staff assignment in order details
7. ✅ **Reports Integration** - Staff reports in reports section

**Total New Pages**: 12+ pages
**Total New Features**: 50+ features
**Integration Points**: Dashboard, Orders, Reports

This module will provide complete staff management capabilities for catering businesses, enabling efficient staff allocation, attendance tracking, and performance monitoring.

