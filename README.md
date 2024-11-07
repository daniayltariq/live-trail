# Real-Time Tracking Feature Prototype Documentation

## Overview

This prototype is a real-time tracking feature built to display the live status of agents checking in and out. The setup includes Laravel 11, Inertia.js with Vue 3 for the frontend, and Laravel Reverb for WebSocket support. This application updates agent check-in and check-out data on the admin’s side in real time.

## App Flow

### 1. Admin Login & WebSocket Connection Setup:
- After the admin logs in, the system establishes a WebSocket connection to enable real-time data updates. This connection allows the admin to receive live updates when users check in or check out.
- The WebSocket serves as a bridge for instantaneous data synchronization between the user’s actions and the admin’s dashboard.

### 2. Data Preloading with Eloquent Queries:
- During the initial load of the admin's dashboard, the system uses an Eloquent query to fetch and preload the necessary data. This data might include users' attendance records, previous check-ins, or other relevant details.
- This ensures that the admin sees a full snapshot of the data as soon as they log in, without requiring any additional actions or delays.

### 3. User Login & Dashboard Display:
- When a user logs in, they are directed to a page where they can see their check-in and check-out buttons. Along with this, their previous day's attendance records are displayed, allowing them to see what time they checked in and out the day before.
- This provides the user with a personalized dashboard, giving them clear and actionable information.

### 4. User Interaction with Check-in/Check-out Buttons:
- Upon clicking the check-in or check-out button, the system immediately updates the corresponding time in the database (e.g., recording the current time or the designated check-out time).
- This data update triggers an event on the backend, indicating that the user's status has changed and needs to be reflected in the admin’s view.

### 5. Real-Time Update to Admin via WebSocket:
- Once the user's data is updated in the database, the system triggers an event that communicates with the WebSocket server, pushing the updated information to the admin's dashboard.
- The admin's dashboard is then instantly updated with the latest check-in or check-out data, providing a live view of the users’ attendance status. This real-time communication ensures the admin is always aware of changes without needing to manually refresh the page.

### 6. Overall Flow:
- The process allows for seamless interaction between the user and the admin system, with real-time data syncing and immediate reflection of any changes made by users.
- This setup leverages Inertia.js for smooth page transitions and dynamic updates, while Laravel and WebSockets handle the backend data management and real-time updates.

##

This detailed flow creates a robust system where both users and admins can track and manage attendance effortlessly, with real-time synchronization providing an efficient and modern solution.
