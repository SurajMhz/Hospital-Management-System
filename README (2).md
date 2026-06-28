# CityMed Hospital Management System

## Requirements
- XAMPP (includes Apache + MySQL + PHP)  
- Download from: https://www.apachefriends.org

---

## Steps to Run

**1. Clone the repository**
```
git clone <your-repo-url>
```

**2. Move the project folder to XAMPP**

Copy the `HospitalManagementSystem` folder into:
```
C:\xampp\htdocs\          (Windows)
/Applications/XAMPP/htdocs/   (Mac)
```

**3. Start XAMPP**

Open XAMPP Control Panel and start **Apache** and **MySQL**.

**4. Set up the database**

- Open your browser and go to: `http://localhost/phpmyadmin`
- Click **Import**
- Select the file: `HospitalManagementSystem/DataBaseConnection/schema.sql`
- Click **Go**

**5. Run the project**

Open your browser and go to:
```
http://localhost/HospitalManagementSystem
```

---

## Default Database Config

Located in `DataBaseConnection/db.php` and `DoctorPanel/db.php`:

| Setting  | Value        |
|----------|--------------|
| Host     | localhost    |
| Username | root         |
| Password | *(empty)*    |
| Database | user_system  |

If your MySQL has a different username or password, update both `db.php` files accordingly.

---

## How to Use

- **Register** as a Doctor or Patient from the homepage
- **Login** to access your panel
- Doctors are redirected to the **Doctor Dashboard**
- Patients are redirected to the main page
