# Catering Management System - Feature Implementation Documentation

**Version:** 1.0  
**Date:** December 2024  
**Status:** Current Implementation

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [System Architecture](#system-architecture)
3. [Feature Modules](#feature-modules)
4. [Technical Components](#technical-components)
5. [Database Models Overview](#database-models-overview)
6. [Routes Summary](#routes-summary)

---

## Executive Summary

The Catering Management System is a comprehensive multi-tenant SaaS application built with Laravel, designed to streamline operations for catering businesses. The system manages orders, customers, inventory, equipment, payments, and reporting in a unified platform with complete tenant isolation.

### Key Capabilities

- **Multi-tenant Architecture**: Complete data isolation between different catering businesses
- **Order Management**: Create and manage event bookings with multiple events per order
- **Customer Management**: Automatic customer creation with mobile number as primary identifier
- **Payment & Invoicing**: Generate invoices, track payments, and manage billing
- **Inventory Management**: Track stock levels, manage stock in/out operations, and receive low stock alerts
- **Equipment Management**: Track equipment inventory and assign equipment to events
- **Comprehensive Reporting**: Generate reports for orders, payments, expenses, customers, and profit/loss analysis
- **Global Search**: Search across orders, customers, inventory, and vendors
- **Role-Based Access Control**: Manage users, roles, and permissions

---

## System Architecture

### Multi-Tenant Implementation

The system uses a **single database with tenant isolation** approach, where all tenant-specific tables include a `tenant_id` column. This architecture provides:

- Cost-effective single database solution
- Easy maintenance and updates
- Scalability to support thousands of tenants
- Industry-standard approach (similar to Slack, Trello, Notion)

#### Tenant Isolation Mechanism

1. **HasTenant Trait**: All tenant-specific models use the `HasTenant` trait which:
   - Automatically filters queries by `tenant_id` using global scopes
   - Automatically sets `tenant_id` when creating new records
   - Ensures data isolation at the model level

2. **TenantMiddleware**: Middleware that sets the tenant context in the application container based on the authenticated user's `tenant_id`

3. **Query Filtering**: All database queries are automatically filtered by `tenant_id` to ensure complete data isolation

### Authentication & Authorization System

- **User Authentication**: Standard Laravel authentication with login, registration, and logout
- **Role-Based Access Control (RBAC)**: 
  - Users can have multiple roles
  - Roles have associated permissions
  - Permissions control access to various features
- **Tenant-Scoped Users**: Each user belongs to a specific tenant and can only access data from their tenant

### Technical Stack

- **Backend**: Laravel (PHP 8.1+)
- **Frontend**: Laravel Blade templates with Tailwind CSS and Flowbite components
- **Database**: MySQL/MariaDB with SQLite for development
- **JavaScript**: Vanilla JavaScript with Chart.js for data visualization
- **PDF Generation**: DomPDF for invoice generation
- **Excel Export**: Maatwebsite Excel package for report exports

---

## Feature Modules

### 1. Authentication & User Management

#### Login/Logout Functionality
- User login with email and password
- Session-based authentication
- Logout functionality
- Guest middleware protection for login/register pages

#### User Registration
- New user registration form
- Automatic tenant assignment during registration
- Password hashing and security

#### Role & Permission Management
- Create, update, and delete roles
- Assign permissions to roles
- Role display names and descriptions
- Tenant-scoped roles and permissions

#### User-Role Assignment
- Assign multiple roles to users
- View user's current roles
- Update role assignments

---

### 2. Dashboard Module

The dashboard provides a comprehensive overview of the catering business with real-time metrics and visualizations.

#### Key Metrics Display
- **Total Orders**: Count of confirmed bookings
- **Upcoming Events**: Count of events scheduled for future dates
- **Pending Payments**: Count of orders with pending or partial payment status
- **Completed Events**: Count of successfully completed events
- **Total Customers**: Total number of customers in the system
- **This Month Revenue**: Sum of all payments received in the current month
- **Total Revenue**: Sum of all payments received

#### Widgets and Lists

**Upcoming Events Widget**
- Displays the next 5 upcoming events
- Shows customer name, event date, and order number
- Sorted by event date (ascending)

**Today's Deliveries**
- Lists all events scheduled for today
- Shows customer name, event time, and order number
- Sorted by event time

**Pending Payments Overview**
- Displays top 5 orders with pending or partial payments
- Shows customer name, order number, and payment status
- Sorted by event date

**Low Stock Alerts**
- Count of inventory items with stock levels at or below minimum threshold
- Quick indicator for inventory management

#### Charts and Visualizations

**Revenue Trend Chart**
- 6-month revenue trend visualization
- Monthly revenue totals displayed as a line chart
- Helps identify revenue patterns and trends

**Orders Over Time Chart**
- 6-month order statistics
- Separate lines for confirmed and completed orders
- Tracks order volume trends

**Payment Status Distribution**
- Pie or bar chart showing distribution of payment statuses
- Categories: Pending, Partial, Paid
- Quick overview of payment collection status

**Monthly Revenue Comparison**
- Comparison between current month and previous month revenue
- Visual representation of month-over-month growth

---

### 3. Customer Management

#### Customer Auto-Creation
- Customers are automatically created when a new order is placed with a mobile number that doesn't exist
- Mobile number serves as the unique identifier per tenant
- Customer information (name, email) is updated if provided in subsequent orders

#### Customer List
- Paginated list of all customers
- Displays customer name, mobile number, email, and order count
- Sorted by creation date (newest first)
- Quick access to customer details

#### Customer Detail View
- Complete customer information
- **Order History**: All orders grouped by order number
  - Shows order number, total amount, status, payment status
  - Displays event dates and order creation dates
  - Groups multiple events under the same order number
- **Payment History**: All payments linked to customer's orders
- **Order Statistics**: Total orders, total amount spent

#### Customer Identification
- **Primary Key**: Mobile number (unique per tenant)
- Email and address are optional fields
- System prevents duplicate customers with the same mobile number within a tenant

---

### 4. Order/Event Booking Management

#### Create Orders with Multiple Events
- Single order can contain multiple events (e.g., morning, afternoon, evening, night snack)
- All events in a batch share the same order number
- Customer information collected once for all events
- Each event can have:
  - Event date
  - Event time (morning, afternoon, evening, night_snack)
  - Event menu description
  - Guest count
  - Order type (full_service, preparation_only)
  - Dish price
  - Total cost

#### Order Number Generation
- Format: `ORD-XXXXXXXX` (8 random uppercase alphanumeric characters)
- Unique per tenant
- Automatically generated when first order in a batch is created
- Reused if adding events to existing order on the same date

#### Order Grouping
- Orders are grouped by `order_number` throughout the system
- Grouped orders share:
  - Customer information
  - Payment status
  - Invoice (if generated)
- Individual events within a group can have different:
  - Event dates
  - Event times
  - Statuses (pending, confirmed, completed, cancelled)

#### Order Status Workflow
- **Pending**: Initial status when order is created
- **Confirmed**: Order is confirmed and ready for execution
- **Completed**: Event has been successfully delivered
- **Cancelled**: Order has been cancelled

#### Payment Status Management
- **Pending**: No payment received
- **Partial**: Some payment received but not full amount
- **Paid**: Full payment received
- Payment status can be updated from order list or payment management page
- Group payment status update: Updates all orders with the same order number

#### Order Calendar View
- Visual monthly calendar displaying all events
- Events shown on their scheduled dates
- Click on event to view order details
- Color-coded or styled based on order status

#### Order Management Operations
- **View Order**: See complete order details including all events in the group
- **Edit Order**: Update order information, event details, status, and payment status
- **Delete Order**: Remove order from the system
- **Generate Invoice**: Create invoice for the order group
- **Assign Equipment**: Assign equipment to events

---

### 5. Payment & Billing

#### Payment List View
- Paginated list of all orders with payment information
- Orders grouped by order number
- Displays:
  - Order number
  - Customer name
  - Total amount
  - Payment status
  - Invoice status (if invoice exists)
- Quick payment status update functionality

#### Group Payment Status Updates
- Update payment status for all orders sharing the same order number
- Bulk status update capability
- Maintains consistency across related orders

#### Invoice Generation
- Generate invoice for an order group (all orders with same order number)
- **Invoice Number Format**: `INV-YYYYMMDD-XXXX`
  - Date prefix (YYYYMMDD)
  - 4-digit sequential number (0001, 0002, etc.)
  - Unique per tenant per day
- **Invoice Calculation**:
  - Total amount: Sum of all order costs in the group
  - Tax: Configurable (currently 0)
  - Discount: Configurable (currently 0)
  - Final amount: Total + Tax - Discount
- Prevents duplicate invoice generation for the same order

#### Invoice Display
- HTML invoice view with:
  - Company information (from tenant profile)
  - Customer information
  - Invoice number and date
  - All events in the order group
  - Itemized list of events with dates, times, guest counts, and costs
  - Payment summary
  - Customizable branding (logo, footer text, terms)

#### PDF Invoice Download
- Generate and download invoice as PDF
- Uses DomPDF library
- Professional invoice formatting
- Includes all invoice branding settings
- Filename format: `invoice-{invoice_number}.pdf`

#### Invoice Branding
- **Company Logo**: URL to company logo displayed on invoices
- **Footer Text**: Custom text displayed at invoice footer
- **Terms & Conditions**: Terms displayed on invoices
- All branding settings are tenant-specific

---

### 6. Inventory Management

#### Inventory Item Management (CRUD)
- **Create**: Add new inventory items with:
  - Name
  - Unit of measurement (kg, liter, piece, etc.)
  - Current stock level
  - Minimum stock threshold
  - Price per unit
  - Description (optional)
- **Read**: View inventory items with stock levels and details
- **Update**: Modify inventory item information
- **Delete**: Remove inventory items from the system

#### Stock In Operations
- Add stock to inventory items
- Record purchase information:
  - Inventory item
  - Quantity added
  - Purchase price (optional)
  - Vendor (optional)
  - Notes (optional)
- Automatically updates current stock level
- Creates stock transaction record

#### Stock Out Operations
- Reduce stock from inventory items
- Record usage information:
  - Inventory item
  - Quantity used
  - Notes (optional)
- Validates available stock before allowing stock out
- Automatically updates current stock level
- Creates stock transaction record

#### Low Stock Alerts
- Automatic detection of items with `current_stock <= minimum_stock`
- Low stock items list page
- Dashboard widget showing count of low stock items
- Helps prevent stockouts

#### Stock Transaction History
- Complete history of all stock in/out operations
- Viewable per inventory item
- Shows:
  - Transaction type (in/out)
  - Quantity
  - Price (for stock in)
  - Vendor (for stock in)
  - Date and time
  - Notes

#### Vendor Integration
- Link stock purchases to vendors
- Track purchases by vendor
- Vendor purchase history available in vendor detail view

---

### 7. Vendor Management

#### Vendor CRUD Operations
- **Create**: Add new vendors with:
  - Name
  - Contact person
  - Phone number
  - Email (optional)
  - Address (optional)
- **Read**: View vendor information and purchase history
- **Update**: Modify vendor details
- **Delete**: Remove vendors from the system

#### Vendor-Transaction Relationship
- Vendors can be linked to stock purchase transactions
- Track which vendor supplied which inventory items
- Purchase history per vendor

#### Vendor Purchase History
- View all stock purchases from a specific vendor
- See inventory items purchased
- View purchase dates and amounts
- Calculate total spending per vendor

---

### 8. Equipment Management

#### Equipment Inventory (CRUD)
- **Create**: Add equipment items with:
  - Name
  - Category (optional)
  - Total quantity
  - Available quantity
  - Status (available, damaged)
- **Read**: View equipment inventory with availability
- **Update**: Modify equipment information
- **Delete**: Remove equipment from the system

#### Equipment Assignment to Orders
- Assign equipment to specific events/orders
- Specify quantity of each equipment item needed
- Validation ensures:
  - Requested quantity doesn't exceed available quantity
  - Equipment is available (status = available)
- Multiple equipment items can be assigned to a single order
- View assigned equipment in order details

#### Available Quantity Tracking
- System tracks total quantity vs. available quantity
- Available quantity decreases when equipment is assigned
- Helps prevent over-allocation of equipment

#### Equipment Status
- **Available**: Equipment is ready for use
- **Damaged**: Equipment is not available due to damage
- Status affects equipment assignment availability

#### Equipment-Order Relationship
- Many-to-many relationship between equipment and orders
- Pivot table stores assigned quantity
- View equipment assignments in equipment detail view

---

### 9. Reports Module

The reports module provides comprehensive business analytics with date filtering, visualizations, and export capabilities.

#### Orders Report
- **Date Range Filtering**: Filter orders by event date range
- **Order Statistics**:
  - Total orders in period
  - Total order amount
  - Count by status (confirmed, completed, pending)
- **Charts**:
  - Order trends over time (daily)
  - Order status distribution
  - Orders by event type
- **Grouped Display**: Orders grouped by order number
- **Export**: Excel export with all order details

#### Payments Report
- **Date Range Filtering**: Filter payments by payment date range
- **Payment Statistics**:
  - Total payments count
  - Total payment amount
  - Breakdown by payment mode (cash, UPI, bank transfer)
- **Charts**:
  - Payment trends over time (daily)
  - Payment method distribution
- **Export**: Excel export with payment details

#### Expenses Report
- **Date Range Filtering**: Filter expenses by transaction date range
- **Expense Statistics**:
  - Total purchases count
  - Total expense amount
  - Breakdown by vendor
- **Charts**:
  - Expense trends over time (daily)
  - Top vendors by expense amount
  - Monthly expense comparison
- **Export**: Excel export with expense details

#### Customers Report
- **Customer Analysis**:
  - All customers with order counts
  - Total spending per customer
  - Returning customers identification (customers with >1 order)
- **Charts**:
  - Top customers by order count
  - Customer order frequency distribution
- **Export**: Excel export with customer statistics

#### Profit/Loss Report
- **Date Range Filtering**: Filter by date range
- **Financial Summary**:
  - Total Revenue (from payments)
  - Total Expenses (from stock purchases)
  - Net Profit/Loss (Revenue - Expenses)
- **Charts**:
  - Revenue vs Expenses comparison (monthly)
  - Profit trend over time
- **Export**: Excel export with P/L summary

#### Export Functionality
- All reports support Excel export
- Export files include:
  - Filtered data based on date range
  - All relevant columns and details
  - Formatted for easy analysis
- Filename includes date range for easy identification

---

### 10. Settings Module

#### General Settings
- Tenant-specific application settings
- Key-value storage system
- Settings are automatically scoped to the current tenant

#### Company Profile Management
- Update tenant company information:
  - Company name
  - Email
  - Phone number
  - Address
  - Logo URL
- Company information used in:
  - Invoices
  - Reports
  - System displays

#### Invoice Branding Configuration
- **Invoice Logo**: URL to logo displayed on invoices
- **Footer Text**: Custom text at invoice footer
- **Terms & Conditions**: Terms displayed on invoices
- All settings are tenant-specific

#### Event Types Management
- Create, update, and delete event types
- Event types include:
  - Name
  - Description (optional)
  - Display order (for sorting)
  - Active status
- Event types can be used for categorizing orders

#### Notification Settings
- Configure notification preferences:
  - SMS enabled/disabled
  - Email enabled/disabled
  - Low stock alerts enabled/disabled
  - Payment reminder notifications enabled/disabled
- Settings control when notifications are sent

---

### 11. Global Search

#### Search Functionality
- Real-time search across multiple entities:
  - **Orders**: Search by order number, customer name, or mobile number
  - **Customers**: Search by name, mobile number, or email
  - **Inventory**: Search by inventory item name
  - **Vendors**: Search by vendor name, contact person, or phone
- Minimum 2 characters required for search
- Returns up to 10 results per entity type

#### Search Results
- Results grouped by entity type
- Each result shows:
  - Title (primary identifier)
  - Subtitle (additional context)
  - Direct link to entity detail page
  - Entity type label
- Results displayed in JSON format for AJAX integration

#### Search Service Implementation
- Centralized search service (`SearchService`)
- Tenant-scoped search (only returns results for current tenant)
- Efficient query optimization
- Case-insensitive search

---

## Technical Components

### Services

#### SearchService
- **Purpose**: Centralized search functionality across multiple entities
- **Methods**:
  - `search()`: Main search method that searches across orders, customers, inventory, and vendors
  - Private methods for searching each entity type
- **Features**:
  - Tenant-scoped results
  - Minimum query length validation
  - Result limiting and formatting

#### InvoiceNumberService
- **Purpose**: Generate unique invoice numbers per tenant
- **Format**: `INV-YYYYMMDD-XXXX`
  - Date prefix (YYYYMMDD format)
  - 4-digit sequential number
- **Features**:
  - Unique per tenant per day
  - Automatic sequence incrementing
  - Race condition handling

#### BreadcrumbService
- **Purpose**: Generate breadcrumb navigation from current route
- **Features**:
  - Automatic breadcrumb generation
  - Handles resource routes, nested routes, and custom routes
  - Formatting for display
  - Active state detection

### Export Classes

#### OrdersExport
- Exports order data to Excel format
- Includes order details, customer information, amounts, and statuses
- Formatted for easy analysis

#### PaymentsExport
- Exports payment data to Excel format
- Includes payment details, invoice information, and payment modes
- Formatted for financial analysis

#### ExpensesExport
- Exports expense/stock purchase data to Excel format
- Includes inventory items, vendors, quantities, and prices
- Formatted for expense tracking

#### ProfitLossExport
- Exports profit/loss summary to Excel format
- Includes revenue, expenses, and net profit/loss
- Formatted for financial reporting

### Notifications

#### LowStockAlertNotification
- Notifies when inventory items reach low stock levels
- Can be sent via SMS or email (based on settings)

#### PaymentReminderNotification
- Sends reminders for pending payments
- Can be sent via SMS or email (based on settings)

#### UpcomingEventNotification
- Notifies about upcoming events
- Helps with event preparation and planning

### Middleware

#### TenantMiddleware
- Sets tenant context in application container
- Extracts tenant_id from authenticated user
- Ensures tenant context is available throughout request lifecycle

---

## Database Models Overview

### Core Models

#### Tenant
- **Purpose**: Represents a catering business (tenant)
- **Key Fields**: name, email, phone, address, logo_url, status
- **Relationships**: Has many users, customers, orders

#### User
- **Purpose**: System users (staff, managers, admins)
- **Key Fields**: name, email, password, tenant_id, role, status
- **Relationships**: 
  - Belongs to tenant
  - Belongs to many roles
- **Methods**: `hasRole()`, `hasPermission()`

#### Customer
- **Purpose**: Customer/client information
- **Key Fields**: tenant_id, name, mobile (unique per tenant), email, address
- **Relationships**: 
  - Belongs to tenant
  - Has many orders
- **Trait**: HasTenant

#### Order
- **Purpose**: Event bookings/orders
- **Key Fields**: tenant_id, customer_id, order_number, event_date, event_time, event_menu, address, order_type, guest_count, estimated_cost, status, payment_status
- **Relationships**: 
  - Belongs to tenant
  - Belongs to customer
  - Has one invoice
  - Belongs to many equipment (with pivot quantity)
- **Trait**: HasTenant

#### Invoice
- **Purpose**: Generated invoices for orders
- **Key Fields**: tenant_id, order_id, invoice_number (unique per tenant), total_amount, tax, discount, final_amount, status
- **Relationships**: 
  - Belongs to tenant
  - Belongs to order
  - Has many payments
- **Trait**: HasTenant
- **Methods**: `relatedOrders()`, `calculateTotalFromOrders()`

#### Payment
- **Purpose**: Payment records
- **Key Fields**: tenant_id, invoice_id, amount, payment_mode, payment_date, reference_number, notes
- **Relationships**: 
  - Belongs to tenant
  - Belongs to invoice
- **Trait**: HasTenant

### Inventory Models

#### InventoryItem
- **Purpose**: Inventory/stock items
- **Key Fields**: tenant_id, name, unit, current_stock, minimum_stock, price_per_unit, description
- **Relationships**: 
  - Belongs to tenant
  - Has many stock transactions
- **Trait**: HasTenant
- **Methods**: `isLowStock()`

#### StockTransaction
- **Purpose**: Stock in/out transaction records
- **Key Fields**: tenant_id, inventory_item_id, type (in/out), quantity, price, vendor_id, notes
- **Relationships**: 
  - Belongs to tenant
  - Belongs to inventory item
  - Belongs to vendor (optional)
- **Trait**: HasTenant

#### Vendor
- **Purpose**: Supplier/vendor information
- **Key Fields**: tenant_id, name, contact_person, phone, email, address
- **Relationships**: 
  - Belongs to tenant
  - Has many stock transactions
- **Trait**: HasTenant

### Equipment Models

#### Equipment
- **Purpose**: Equipment/asset inventory
- **Key Fields**: tenant_id, name, category, quantity, available_quantity, status
- **Relationships**: 
  - Belongs to tenant
  - Belongs to many orders (with pivot quantity)
- **Trait**: HasTenant

#### EventEquipment
- **Purpose**: Pivot table for equipment-order assignments
- **Key Fields**: order_id, equipment_id, quantity

### Settings Models

#### Setting
- **Purpose**: Tenant-specific application settings
- **Key Fields**: tenant_id, key, value, type, description
- **Trait**: HasTenant
- **Static Methods**: `getValue()`, `setValue()`

#### EventType
- **Purpose**: Event type categories
- **Key Fields**: tenant_id, name, description, display_order, is_active
- **Relationships**: Belongs to tenant
- **Trait**: HasTenant

### Authorization Models

#### Role
- **Purpose**: User roles
- **Key Fields**: tenant_id, name (unique per tenant), display_name, description
- **Relationships**: 
  - Belongs to tenant
  - Belongs to many users
  - Belongs to many permissions

#### Permission
- **Purpose**: System permissions
- **Key Fields**: tenant_id, name, display_name, description
- **Relationships**: 
  - Belongs to tenant
  - Belongs to many roles

---

## Routes Summary

### Public Routes
- `GET /` - Welcome page
- `GET /login` - Login form
- `POST /login` - Process login
- `GET /register` - Registration form
- `POST /register` - Process registration
- `GET /forgot-password` - Forgot password page
- `GET /lock-screen` - Lock screen page
- `POST /logout` - Logout

### Protected Routes (Requires Authentication & Tenant Context)

#### Dashboard
- `GET /dashboard` - Dashboard home

#### Orders
- `GET /orders` - Order list
- `GET /orders/create` - Create order form
- `POST /orders` - Store new order
- `GET /orders/{order}` - View order details
- `GET /orders/{order}/edit` - Edit order form
- `PUT /orders/{order}` - Update order
- `DELETE /orders/{order}` - Delete order
- `GET /orders/calendar` - Order calendar view

#### Customers
- `GET /customers` - Customer list
- `GET /customers/{customer}` - View customer details

#### Payments
- `GET /payments` - Payment list
- `POST /payments/update-group` - Update group payment status

#### Invoices
- `GET /invoices` - Invoice list
- `GET /invoices/generate/{orderNumber}` - Generate invoice
- `GET /invoices/{invoice}` - View invoice
- `GET /invoices/{invoice}/download` - Download PDF invoice

#### Inventory
- `GET /inventory` - Inventory list
- `GET /inventory/create` - Create inventory item form
- `POST /inventory` - Store inventory item
- `GET /inventory/{inventoryItem}` - View inventory item
- `GET /inventory/{inventoryItem}/edit` - Edit inventory item form
- `PUT /inventory/{inventoryItem}` - Update inventory item
- `DELETE /inventory/{inventoryItem}` - Delete inventory item
- `GET /inventory/stock-in` - Stock in form
- `POST /inventory/stock-in` - Process stock in
- `GET /inventory/stock-out` - Stock out form
- `POST /inventory/stock-out` - Process stock out
- `GET /inventory/low-stock` - Low stock items list

#### Vendors
- `GET /vendors` - Vendor list
- `GET /vendors/create` - Create vendor form
- `POST /vendors` - Store vendor
- `GET /vendors/{vendor}` - View vendor details
- `GET /vendors/{vendor}/edit` - Edit vendor form
- `PUT /vendors/{vendor}` - Update vendor
- `DELETE /vendors/{vendor}` - Delete vendor

#### Equipment
- `GET /equipment` - Equipment list
- `GET /equipment/create` - Create equipment form
- `POST /equipment` - Store equipment
- `GET /equipment/{equipment}` - View equipment details
- `GET /equipment/{equipment}/edit` - Edit equipment form
- `PUT /equipment/{equipment}` - Update equipment
- `DELETE /equipment/{equipment}` - Delete equipment
- `GET /orders/{order}/assign-equipment` - Assign equipment form
- `POST /orders/{order}/assign-equipment` - Process equipment assignment

#### Reports
- `GET /reports/orders` - Orders report
- `GET /reports/payments` - Payments report
- `GET /reports/expenses` - Expenses report
- `GET /reports/customers` - Customers report
- `GET /reports/profit-loss` - Profit/Loss report
- `GET /reports/export` - Export report data

#### Settings
- `GET /settings` - Settings index
- `POST /settings` - Update settings
- `GET /settings/company-profile` - Company profile form
- `POST /settings/company-profile` - Update company profile
- `GET /settings/invoice-branding` - Invoice branding form
- `POST /settings/invoice-branding` - Update invoice branding
- `GET /settings/event-types` - Event types list
- `POST /settings/event-types` - Create event type
- `PUT /settings/event-types/{eventType}` - Update event type
- `DELETE /settings/event-types/{eventType}` - Delete event type
- `GET /settings/notifications` - Notification settings form
- `POST /settings/notifications` - Update notification settings

#### Roles & Permissions
- `GET /roles` - Roles list
- `POST /roles` - Create role
- `PUT /roles/{role}` - Update role
- `DELETE /roles/{role}` - Delete role
- `GET /users/{user}/assign-roles` - Assign roles form
- `POST /users/{user}/assign-roles` - Process role assignment

#### Global Search
- `GET /search` - Global search (returns JSON)

---

## System Workflows

### Order Creation Workflow
1. User creates new order with customer information
2. System checks if customer exists (by mobile number)
3. If customer doesn't exist, creates new customer automatically
4. If customer exists, updates customer information if different
5. System generates or reuses order number
6. Creates order records for each event in the batch
7. All events share the same order number
8. Orders start with "pending" status and "pending" payment status

### Invoice Generation Workflow
1. User selects order group (by order number)
2. System checks if invoice already exists
3. If invoice exists, redirects to existing invoice
4. If no invoice, calculates totals from all orders in group
5. Generates unique invoice number using InvoiceNumberService
6. Creates invoice record
7. User can view invoice and download as PDF

### Payment Tracking Workflow
1. Payments are recorded against invoices
2. Payment status can be updated at order level
3. Group payment status update affects all orders with same order number
4. Payment history is tracked per invoice
5. Payment status flows: Pending → Partial → Paid

### Inventory Stock Management Workflow
1. Stock In: User records purchase, system updates current_stock
2. Stock Out: User records usage, system validates and updates current_stock
3. Low Stock Detection: System automatically detects items at or below minimum_stock
4. Alerts: System can send notifications for low stock items

### Equipment Assignment Workflow
1. User selects order/event
2. System shows available equipment
3. User selects equipment and quantities
4. System validates available quantities
5. System creates equipment-order assignments
6. Available quantity is tracked but not automatically decremented

---

## Key Features Summary

✅ **Multi-tenant Architecture** - Complete data isolation  
✅ **Order Management** - Create, edit, delete orders with multiple events  
✅ **Customer Management** - Auto-creation with mobile number as identifier  
✅ **Payment & Invoicing** - Generate invoices, track payments, PDF download  
✅ **Inventory Management** - Stock tracking, in/out operations, low stock alerts  
✅ **Vendor Management** - Supplier information and purchase tracking  
✅ **Equipment Management** - Equipment inventory and event assignment  
✅ **Comprehensive Reporting** - Orders, payments, expenses, customers, P/L  
✅ **Global Search** - Search across all major entities  
✅ **Role-Based Access Control** - Users, roles, and permissions  
✅ **Settings Management** - Company profile, invoice branding, notifications  
✅ **Calendar View** - Visual event calendar  
✅ **Excel Export** - Export reports to Excel format  
✅ **Dashboard Analytics** - Metrics, charts, and widgets  

---

**Document Status**: Complete  
**Last Updated**: December 2024  
**Maintained By**: Development Team

