# Catering Management System
## Client Documentation

---

## Project Information

### Project Name
**Catering Management System**

### Purpose
The Catering Management System is a comprehensive multi-tenant SaaS application designed to streamline operations for catering businesses. The system provides an all-in-one solution that automates order management, customer tracking, inventory control, staff management, equipment allocation, payment processing, and financial reporting in a unified platform.

### Overview
This system enables catering businesses to:
- Automate order management and customer tracking
- Streamline payment processing and invoicing
- Optimize staff and equipment allocation for events
- Track inventory in real-time with low stock alerts
- Generate comprehensive reports and analytics for data-driven decision-making
- Support multiple catering businesses through a multi-tenant architecture

The system is designed to reduce order processing time, improve payment collection rates, enhance customer retention, and provide accurate financial reporting and profit/loss analysis.

---

## System Features (Menu-wise)

### 1. Dashboard
Provides a quick overview of your catering business with key metrics and alerts.

**Features:**
- Total Orders: Displays total confirmed bookings
- Upcoming Events: List of events happening today/tomorrow/this week
- Pending Payments: Clients who haven't paid full amount
- Completed Events: Events already delivered
- Today's Deliveries: What needs to be delivered today
- Alerts & Notifications: Low stock warnings, upcoming payment reminders, staff task assignments

---

### 2. Orders
Handle all booking details and event management.

**Sub-Menus:**
- **Orders List**: View, create, edit, and manage all event bookings with essential information including customer details, event dates, status, and payment information
- **Order Calendar**: Visual monthly calendar view displaying all scheduled events for easy planning and scheduling

**Features:**
- Create new event orders with customer details, event date, time, address, order type, guest count, menu package, and estimated cost
- Edit and update booking details
- Track order status workflow (Pending → Confirmed → Completed)
- Manage payment status from order list
- Automatic customer creation when new mobile number is used
- Quick search and filter functionality
- Export options for order data

---

### 3. Customers
Manage customers automatically using mobile number as the primary identifier.

**Features:**
- Automatic customer creation on first booking
- Mobile number-based customer identification
- Complete order history per customer
- Payment tracking per customer
- Customer details showing full order history, payments, and event dates
- Search and filter functionality

---

### 4. Payments
Manage invoices and payments efficiently.

**Features:**
- View all payments with filtering options
- Record advance and final payments
- Track pending and partial payments
- Multiple payment modes support (Cash, UPI, Bank Transfer, Credit Card)
- Payment history tracking
- Group payment status updates
- Payment receipt generation

---

### 5. Invoices
Automatic invoice generation and management.

**Features:**
- Auto-generated invoices based on event details
- View all invoices with filtering
- Download invoices as PDF
- Printable invoice format
- Company branding on invoices
- Invoice number tracking

---

### 6. Inventory
Track all ingredients and stock usage in real-time.

**Sub-Menus:**
- **Item List**: View and manage all inventory items with current stock levels, minimum stock thresholds, and pricing
- **Stock In**: Record new stock purchases and additions to inventory
- **Stock Out**: Record stock usage and reductions after events

**Features:**
- Real-time stock tracking
- Low stock alerts and automatic warnings
- Purchase history tracking
- Stock adjustment operations (in/out)
- Inventory item management with units and pricing
- Vendor-linked stock transactions

---

### 7. Vendors
Manage supplier information and vendor contacts.

**Features:**
- Complete vendor directory with contact information
- Vendor contact person, phone, email, and address management
- Link vendors to stock transactions
- Vendor history and purchase tracking

---

### 8. Equipment
Track chairs, tables, vessels, heaters, and other equipment.

**Features:**
- Equipment inventory tracking with quantities and current status
- Equipment assignment to specific events
- Availability checking before event assignment
- Equipment categories management
- Maintenance scheduling capabilities

---

### 9. Staff
Assign staff for events and track their performance.

**Features:**
- Complete staff directory with contact information
- Staff role management (Cook, Waiter, Manager, etc.)
- Event-wise staff assignment
- Staff workload and performance tracking
- Staff status management (active/inactive)

---

### 10. Attendance
Track staff working days and attendance records.

**Sub-Menus:**
- **Attendance List**: View all attendance records with date, staff, and status
- **Mark Attendance**: Record individual staff attendance for specific dates
- **Bulk Mark**: Mark attendance for multiple staff members at once

**Features:**
- Daily attendance tracking
- Present/Absent status recording
- Attendance notes and remarks
- Staff attendance history
- Attendance reports

---

### 11. Reports
View business performance and analytics.

**Sub-Menus:**
- **Orders Report**: Daily/Monthly bookings analysis with date filters
- **Payments Report**: Revenue reports with date range filtering
- **Expenses Report**: Purchases, staff, ingredients cost analysis
- **Customers Report**: Returning customer analysis and customer history
- **Profit & Loss Report**: Full business overview with financial insights including revenue, expenses, and net profit
- **Attendance Report**: Staff attendance analysis and statistics

**Features:**
- Date range filtering for all reports
- Export to PDF/Excel functionality
- Graphical representations and charts
- Comparative analysis
- Real-time data updates
- Comprehensive business insights

---

### 12. Settings
System customization and configuration.

**Sub-Menus:**
- **User Management**: Create and manage admin/staff accounts with role assignments
- **Roles & Permissions**: Define access rules and permissions for different user types
- **Order Statuses**: Manage custom order status types (Pending, Confirmed, Completed, Cancelled, etc.)
- **Order Event Times**: Configure event time slots (Morning, Afternoon, Evening, Night Snack, etc.)
- **Order Types**: Manage event categories (Wedding, Birthday, Corporate, etc.)
- **Inventory Units**: Configure measurement units for inventory items (kg, liter, piece, etc.)
- **Equipment Categories**: Organize equipment by categories
- **Staff Roles**: Define staff role types (Cook, Waiter, Manager, etc.)

**Features:**
- Customizable system settings
- Role-based access control configuration
- System preferences management
- Data configuration for dropdowns and selections

---

## Role Permissions

The system supports three main user roles with different permission levels:

### Admin Role
**Description:** Full system access with complete control over all modules and settings.

**Permissions:**
- Full access to all modules: Orders, Customers, Inventory, Invoices, Payments, Reports, Users, Roles, Vendors, Equipment, Staff, Attendance
- All actions allowed: View, Create, Edit, Delete, Export
- User and role management access
- System settings configuration
- Complete financial and operational data access

**Key Capabilities:**
- Create, edit, and delete any record
- Manage users and assign roles
- Configure system settings
- Access all reports and analytics
- Export data in various formats

---

### Manager Role
**Description:** Management access for day-to-day operations without user/role management capabilities.

**Permissions:**
- Full access to operational modules: Orders, Customers, Inventory, Invoices, Payments, Reports, Vendors, Equipment, Staff, Attendance
- All actions allowed: View, Create, Edit, Delete, Export
- **Restricted from:** User Management and Role Management modules

**Key Capabilities:**
- Manage orders, customers, and bookings
- Process payments and generate invoices
- Track inventory and manage stock
- Assign staff and equipment to events
- View and generate reports
- Cannot create or modify users and roles

---

### Staff Role
**Description:** Limited access for viewing and creating records only, suitable for operational staff.

**Permissions:**
- **View Access:** Can view all modules (Orders, Customers, Inventory, Invoices, Payments, Reports, Vendors, Equipment, Staff, Attendance)
- **Create Access:** Can create new records in all modules
- **Restricted from:** Edit, Delete, and Export actions
- **Restricted from:** User Management and Role Management modules

**Key Capabilities:**
- View orders, customers, and inventory
- Create new orders and bookings
- Record attendance
- View reports (read-only)
- Cannot modify or delete existing records
- Cannot export data
- Cannot manage users or system settings

---

## Permission Matrix

| Module | Action | Admin | Manager | Staff |
|--------|--------|-------|---------|-------|
| **Orders** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Customers** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Inventory** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Invoices** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Payments** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Vendors** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Equipment** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Staff** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Attendance** | View | ✅ | ✅ | ✅ |
| | Create | ✅ | ✅ | ✅ |
| | Edit | ✅ | ✅ | ❌ |
| | Delete | ✅ | ✅ | ❌ |
| | Export | ✅ | ✅ | ❌ |
| **Reports** | View | ✅ | ✅ | ✅ |
| | Export | ✅ | ✅ | ❌ |
| **Users** | View | ✅ | ❌ | ❌ |
| | Create | ✅ | ❌ | ❌ |
| | Edit | ✅ | ❌ | ❌ |
| | Delete | ✅ | ❌ | ❌ |
| **Roles** | View | ✅ | ❌ | ❌ |
| | Create | ✅ | ❌ | ❌ |
| | Edit | ✅ | ❌ | ❌ |
| | Delete | ✅ | ❌ | ❌ |

**Legend:**
- ✅ = Allowed
- ❌ = Not Allowed

---

## Summary

The Catering Management System provides a comprehensive solution for managing all aspects of a catering business. With role-based access control, the system ensures that users have appropriate access levels based on their responsibilities. The Admin role has complete system control, Managers can handle day-to-day operations without user management, and Staff members can view and create records as needed for their operational tasks.

---

**Document Version:** 1.0  
**Last Updated:** January 2025  
**Prepared for:** Client Delivery

