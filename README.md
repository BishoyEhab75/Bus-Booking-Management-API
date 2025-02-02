# Bus Booking API

## 📌 Overview

This is a Laravel-based Bus Booking Management API that allows users to book bus trips between predefined stations. It manages buses, trips, trip stops, and seat availability dynamically.

## 🚀 Features

- **User Authentication** (Admin & Normal Users)
- **Manage Stations, Buses, Trips & Trip Stops** (Admin Only)
- **Dynamic Seat Availability**
- **Prevent Overbooking**
- **Fetch Available Seats for a Trip**
- **Users Can Book a Seat on a Trip**

## 🛠️ Installation

### **1️⃣ Clone the Repository**

```bash
git clone git@github.com:BishoyEhab75/Bus-Booking-Management-API.git
cd Bus-Booking-Management-API
```

### **2️⃣ Install Dependencies**

```bash
composer install
npm install
```

### **3️⃣ Environment Setup**

```bash
cp .env.example .env
php artisan key:generate
```

Set your database credentials in `.env`.

### **4️⃣ Run Migrations & Seed Data**

```bash
php artisan migrate
```

### **5️⃣ Start the Application**

```bash
php artisan serve
```

## 🔐 Authentication

- **Register:** `POST /api/register`
- **Login:** `POST /api/login`
- Use the returned token in **Authorization: Bearer {token}** for protected routes.

## 📌 API Endpoints

### **1️⃣ Admin Endpoints (Require Admin Role)**

- **Create Station:** `POST /api/stations`
- **Create Bus:** `POST /api/buses`
- **Create Trip:** `POST /api/trips`
- **Add Trip Stops:** `POST /api/trip-stops`

### **2️⃣ User Endpoints**

- **Get Available Seats for a Trip:** `GET /api/available-seats?from_station={id}&to_station={id}`
- **Book a Seat:** `POST /api/bookings`
- **View My Bookings:** `GET /api/bookings`

## 🔍 Example Request (Postman)

### **Booking a Seat**

```json
{
  "from_station": 1,
  "to_station": 4
}
```

Headers:

```bash
Authorization: Bearer {user_token}
```

### **Response**

```json
{
  "trip_id": 3,
  "available_seats": [2, 5, 7, 9]
}
```

## 🏗️ Future Improvements

- Implement Notifications
- Add Payment Integration
- Enhanced Trip Scheduling

## 🤝 Contributing

1. Fork the repository
2. Create a new branch (`feature-xyz`)
3. Commit your changes
4. Push to your branch and open a PR

## 📜 License

This project is licensed under the MIT License.
