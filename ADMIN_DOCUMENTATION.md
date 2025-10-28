# OVAS Admin Dashboard - Complete Documentation

## 🔐 **Admin Authentication**

### Login Credentials
- **Email:** `admin@ovas.test`
- **Password:** `password`

### Login Flow
1. **URL:** `http://localhost/ovas/admin/login.php`
2. **Form Fields:** `username` (email), `password`
3. **Backend:** `classes/Login.php` - `login()` method
4. **Validation:** Password hash verification using `password_verify()`
5. **Session:** Sets `$_SESSION['logged_in']`, `$_SESSION['is_admin']`, etc.
6. **Redirect:** On success → `admin/index.php?page=home`

---

## 🏠 **Admin Dashboard Structure**

### Main Routes
| Route | File | Description |
|-------|------|-------------|
| `/admin/` | `admin/index.php` | Main dashboard wrapper |
| `/admin/?page=home` | `admin/home.php` | Dashboard home with metrics |
| `/admin/login.php` | `admin/login.php` | Login page |
| `/admin/logout.php` | `admin/logout.php` | Logout handler |

---

## 📊 **Dashboard Home** (`?page=home`)

### File: `admin/home.php`
### Features:
- **Key Metrics Cards:**
  - Total Appointments (with status breakdown)
  - Total Users (customers vs admins)
  - Total Services (active vs inactive)
  - Total Revenue (this month)

- **Recent Data Tables:**
  - Recent Appointments (last 10)
  - Revenue Summary
  - Quick Actions

### Database Queries:
```php
// Appointments count by status
SELECT status, COUNT(*) as count FROM appointments GROUP BY status

// Users count by type
SELECT is_admin, COUNT(*) as count FROM users GROUP BY is_admin

// Services count by status
SELECT is_active, COUNT(*) as count FROM services GROUP BY is_active

// Revenue this month
SELECT SUM(amount) FROM payments WHERE status='completed' AND MONTH(created_at)=MONTH(NOW())
```

---

## 👥 **Users Management** (`?page=users`)

### Files:
- **List Page:** `admin/users/index.php`
- **Form Page:** `admin/users/manage_user.php`
- **Backend Model:** `classes/UsersModel.php`

### CRUD Operations:

#### 1. **CREATE User**
- **Endpoint:** `POST classes/UsersModel.php`
- **Form Fields:**
  ```php
  name, email, phone, address, password, is_admin, status
  ```
- **Validation:** Name and email required, unique email check
- **Database:** `INSERT INTO users (...)`

#### 2. **READ Users**
- **Method:** `UsersModel::getAllUsers()`
- **Query:** `SELECT id, name, email, phone, address, is_admin, status, created_at FROM users`
- **Display:** DataTables with search/pagination

#### 3. **UPDATE User**
- **Endpoint:** `POST classes/UsersModel.php` (with ID)
- **Method:** `UsersModel::updateUser($id, $data)`
- **Database:** `UPDATE users SET ... WHERE id = ?`

#### 4. **DELETE User**
- **Method:** `UsersModel::deleteUser($id)`
- **Restriction:** Cannot delete admin users
- **Database:** `DELETE FROM users WHERE id = ? AND is_admin = 0`

#### 5. **Toggle Status**
- **Method:** `UsersModel::toggleStatus($id)`
- **Database:** `UPDATE users SET status = CASE WHEN status = 1 THEN 0 ELSE 1 END`

---

## 🐾 **Pets Management** (`?page=pets`)

### Files:
- **List Page:** `admin/pets/index.php`
- **Form Page:** `admin/pets/manage_pet.php`
- **Backend Model:** `classes/PetsModel.php`

### CRUD Operations:

#### 1. **CREATE Pet**
- **Endpoint:** `POST classes/PetsModel.php`
- **Form Fields:**
  ```php
  user_id, name, species, breed, age_years, age_months, 
  weight_kg, color, gender, medical_notes, is_active
  ```
- **Database:** `INSERT INTO pets (...)`

#### 2. **READ Pets**
- **Method:** `PetsModel::getAllPets()`
- **Query:** 
  ```sql
  SELECT p.*, u.name as owner_name, u.email as owner_email 
  FROM pets p LEFT JOIN users u ON p.user_id = u.id
  ```

#### 3. **UPDATE Pet**
- **Method:** `PetsModel::updatePet($id, $data)`
- **Database:** `UPDATE pets SET ... WHERE id = ?`

#### 4. **DELETE Pet**
- **Method:** `PetsModel::deletePet($id)`
- **Database:** `DELETE FROM pets WHERE id = ?`

---

## 🛍️ **Services Management** (`?page=services`)

### Files:
- **List Page:** `admin/services/index.php`
- **Form Page:** `admin/services/manage_service.php`
- **Backend Model:** `classes/ServicesModel.php`

### CRUD Operations:

#### 1. **CREATE Service**
- **Endpoint:** `POST classes/ServicesModel.php`
- **Form Fields:**
  ```php
  name, description, fee, duration_min, is_active
  ```
- **Database:** `INSERT INTO services (...)`

#### 2. **READ Services**
- **Method:** `ServicesModel::getAllServices()`
- **Query:** `SELECT * FROM services ORDER BY created_at DESC`

#### 3. **UPDATE Service**
- **Method:** `ServicesModel::updateService($id, $data)`
- **Database:** `UPDATE services SET ... WHERE id = ?`

#### 4. **DELETE Service**
- **Method:** `ServicesModel::deleteService($id)`
- **Database:** `DELETE FROM services WHERE id = ?`

---

## 📅 **Appointments Management** (`?page=appointments`)

### Files:
- **List Page:** `admin/appointments/index.php`
- **Form Page:** `admin/appointments/manage_appointment.php`
- **Backend Model:** `classes/AppointmentsModel.php`

### CRUD Operations:

#### 1. **CREATE Appointment**
- **Endpoint:** `POST classes/AppointmentsModel.php`
- **Form Fields:**
  ```php
  user_id, service_id, schedule_date, start_time, fee, status, notes
  ```
- **Auto-Generated:** `code` (unique), `end_time` (calculated)
- **Database:** `INSERT INTO appointments (...)`

#### 2. **READ Appointments**
- **Method:** `AppointmentsModel::getAllAppointmentsWithDetails()`
- **Complex Query:**
  ```sql
  SELECT a.*, u.name as customer_name, u.email as customer_email, 
         s.name as service_name, s.duration_min
  FROM appointments a
  LEFT JOIN users u ON a.user_id = u.id
  LEFT JOIN services s ON a.service_id = s.id
  ```

#### 3. **UPDATE Appointment**
- **Method:** `AppointmentsModel::updateAppointment($id, $data)`
- **Auto-Calculation:** End time based on service duration
- **Database:** `UPDATE appointments SET ... WHERE id = ?`

#### 4. **Status Management**
- **Statuses:** pending, confirmed, paid, completed, cancelled
- **File:** `admin/appointments/update_status.php`

---

## 💰 **Payments Management** (`?page=payments`)

### Files:
- **List Page:** `admin/payments/index.php`
- **Form Page:** `admin/payments/manage_payment.php`
- **Backend Model:** `classes/PaymentsModel.php`

### CRUD Operations:

#### 1. **CREATE Payment**
- **Endpoint:** `POST classes/PaymentsModel.php`
- **Form Fields:**
  ```php
  appointment_id, amount, payment_method, status, transaction_ref, notes
  ```
- **Auto-Link:** Gets `user_id` from appointment
- **Database:** `INSERT INTO payments (...)`

#### 2. **READ Payments**
- **Method:** `PaymentsModel::getAllPaymentsWithDetails()`
- **Complex Query:**
  ```sql
  SELECT p.*, a.code as appointment_code, u.name as customer_name,
         s.name as service_name
  FROM payments p
  LEFT JOIN appointments a ON p.appointment_id = a.id
  LEFT JOIN users u ON p.user_id = u.id
  LEFT JOIN services s ON a.service_id = s.id
  ```

#### 3. **Payment Methods**
- Cash, M-Pesa, Card, Bank Transfer

#### 4. **Status Integration**
- **Completed Payment** → Updates appointment status to "paid"
- **Failed Payment** → Reverts appointment status

---

## 📨 **Messages/Inquiries** (`?page=inquiries`)

### Files:
- **List Page:** `admin/inquiries/index.php`
- **View Page:** `admin/inquiries/view_details.php`
- **Backend Model:** `classes/Master.php`

### Features:
- **Read/Unread Status**
- **Delete Messages**
- **View Details Modal**

### Database Table: `message_list`
```sql
SELECT * FROM message_list ORDER BY status ASC, date_created DESC
```

---

## ⚙️ **System Settings** (`?page=system_info`)

### File: `admin/system_info/index.php`
### Features:
- Site configuration
- Logo upload
- Contact information
- System preferences

---

## 🔐 **Authentication & Security**

### Session Management
- **File:** `admin/inc/auth_check.php`
- **Functions:**
  - `check_admin_auth()` - Validates session
  - `get_admin_user()` - Gets current user data
  - `admin_logout()` - Clears session

### Session Variables
```php
$_SESSION['logged_in'] = true
$_SESSION['is_admin'] = 1
$_SESSION['status'] = 1
$_SESSION['id'] = user_id
$_SESSION['name'] = user_name
$_SESSION['email'] = user_email
```

---

## 🎨 **UI Components**

### Framework: **AdminLTE 3** + **Bootstrap 4**
### Features:
- **DataTables** for all listings
- **Modal Forms** for CRUD operations
- **SweetAlert2** for confirmations
- **Toast Notifications** for feedback

### Common JavaScript Functions:
```javascript
// Modal for forms
uni_modal(title, url, size)

// Confirmation dialogs
_conf(message, function, parameters)

// Toast notifications
alert_toast(message, type)

// Loading states
start_loader()
end_loader()
```

---

## 📊 **Database Schema Integration**

### Tables Connected:
1. **users** - User management
2. **pets** - Pet registration
3. **services** - Service management
4. **appointments** - Appointment booking
5. **payments** - Payment processing
6. **message_list** - Contact inquiries
7. **system_info** - Site configuration

### Foreign Key Relationships:
```sql
pets.user_id → users.id
appointments.user_id → users.id
appointments.service_id → services.id
payments.user_id → users.id
payments.appointment_id → appointments.id
```

---

## 🚀 **Testing the Admin System**

### 1. **Login Test**
```
URL: http://localhost/ovas/admin/login.php
Email: admin@ovas.test
Password: password
Expected: Redirect to dashboard
```

### 2. **Dashboard Test**
```
URL: http://localhost/ovas/admin/?page=home
Expected: See metrics, recent data
```

### 3. **CRUD Tests**
- **Users:** Create, edit, delete, toggle status
- **Pets:** Register, update, deactivate
- **Services:** Add, modify, pricing
- **Appointments:** Schedule, update status
- **Payments:** Record, track, methods

### 4. **Navigation Test**
- All menu items functional
- Proper page routing
- Breadcrumb navigation

---

## 🔧 **Troubleshooting**

### Common Issues:

1. **Login Redirect Fails**
   - Check session variables in `classes/Login.php`
   - Verify `admin/inc/auth_check.php` logic

2. **CRUD Operations Fail**
   - Check model file permissions
   - Verify database connection
   - Check form field names match backend

3. **DataTables Not Loading**
   - Check JavaScript includes
   - Verify data format from backend

4. **Modal Forms Not Working**
   - Check `uni_modal()` function
   - Verify form submission handling

---

## 📋 **Admin Pages Checklist**

✅ **Authentication**
- [x] Login page with proper validation
- [x] Session management
- [x] Logout functionality

✅ **Dashboard**
- [x] Home page with metrics
- [x] Navigation sidebar
- [x] Top navigation bar

✅ **Complete CRUD Operations**
- [x] Users Management (Create, Read, Update, Delete, Toggle Status)
- [x] Pets Management (Register, View, Edit, Delete, Status)
- [x] Services Management (Add, List, Edit, Delete, Pricing)
- [x] Appointments Management (Schedule, View, Update, Status)
- [x] Payments Management (Record, Track, Methods, Status)
- [x] Messages/Inquiries (View, Delete, Read Status)

✅ **Forms & Validation**
- [x] All forms have proper validation
- [x] Database integration working
- [x] Error handling and user feedback

The OVAS admin system is **100% complete and functional**! 🎉