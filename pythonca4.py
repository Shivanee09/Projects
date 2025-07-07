import tkinter as tk
from tkinter import messagebox
import mysql.connector


def connect_db():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="shivanee@123",  
            database="eating_habits"
        )
        cursor = conn.cursor()
        print("Database connected successfully!")
        return conn, cursor
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        exit()


conn, cursor = connect_db()


def register_user():
    username = reg_username_var.get()
    password = reg_password_var.get()

    if username and password:
        try:
            cursor.execute("INSERT INTO users (username, password) VALUES (%s, %s)", (username, password))
            conn.commit()
            messagebox.showinfo("Success", "Registration successful! You can now log in.")
            registration_window.destroy()
        except mysql.connector.IntegrityError:
            messagebox.showerror("Error", "This username is already registered.")
    else:
        messagebox.showerror("Error", "Please fill in all fields.")

def open_registration_page():
    global registration_window, reg_username_var, reg_password_var

    registration_window = tk.Toplevel(login_window)
    registration_window.title("Register")
    registration_window.geometry("400x300")
    registration_window.config(bg="#f0f8ff")

    tk.Label(registration_window, text="Username:").pack(pady=10)
    reg_username_var = tk.StringVar()
    tk.Entry(registration_window, textvariable=reg_username_var).pack(pady=5)

    tk.Label(registration_window, text="Name:").pack(pady=10)
    reg_name_var = tk.StringVar()
    tk.Entry(registration_window, textvariable=reg_name_var).pack(pady=5)

    
    tk.Label(registration_window, text="email:").pack(pady=10)
    reg_email_var = tk.StringVar()
    tk.Entry(registration_window, textvariable=reg_email_var).pack(pady=5)

    tk.Label(registration_window, text="Password:").pack(pady=10)
    reg_password_var = tk.StringVar()
    tk.Entry(registration_window, textvariable=reg_password_var, show="*").pack(pady=5)

    tk.Button(registration_window, text="Register", command=register_user).pack(pady=20)

    
    tk.Button(registration_window, text="Submit", command=register_user).pack(pady=10)


def login_user():
    username = login_username_var.get()
    password = login_password_var.get()

    if username and password:
        cursor.execute("SELECT * FROM users WHERE username = %s AND password = %s", (username, password))
        user = cursor.fetchone()

        if user:
            messagebox.showinfo("Success", "Login successful!")
            login_window.destroy()
            open_survey_form()
        else:
            messagebox.showerror("Error", "Incorrect username or password.")
    else:
        messagebox.showerror("Error", "Please fill in all fields.")

def open_survey_form():
    global root, name_entry, age_var, mood_var, meals_var, snack_var, water_entry, note_entry

    root = tk.Tk()
    root.title("Survey Form")
    root.geometry("400x500")
    root.config(bg="#f0f8ff")

    tk.Label(root, text="Name:").pack(pady=5)
    name_entry = tk.Entry(root, width=30)
    name_entry.pack(pady=5)

    tk.Label(root, text="Age Group:").pack(pady=5)
    age_var = tk.StringVar()
    age_options = ["Under 18", "18-24", "25-34", "35-44", "45+"]
    tk.OptionMenu(root, age_var, *age_options).pack(pady=5)

    tk.Label(root, text="Mood:").pack(pady=5)
    mood_var = tk.StringVar()
    mood_options = ["Happy", "Neutral", "Stressed", "Sad"]
    tk.OptionMenu(root, mood_var, *mood_options).pack(pady=5)

    tk.Label(root, text="Number of Meals:").pack(pady=5)
    meals_var = tk.StringVar()
    meals_options = ["1 meal", "2 meals", "3 meals", "4+ meals"]
    tk.OptionMenu(root, meals_var, *meals_options).pack(pady=5)

    tk.Label(root, text="Did you have snacks?").pack(pady=5)
    snack_var = tk.BooleanVar()
    tk.Checkbutton(root, text="Yes", variable=snack_var).pack(pady=5)

    tk.Label(root, text="Water Consumed (in glasses):").pack(pady=5)
    water_entry = tk.Entry(root, width=10)
    water_entry.pack(pady=5)

    tk.Label(root, text="Additional Notes:").pack(pady=5)
    note_entry = tk.Text(root, width=30, height=5)
    note_entry.pack(pady=5)


    tk.Button(root, text="Submit", command=submit_data).pack(pady=10)

def submit_data():
    name = name_entry.get()
    age_group = age_var.get()
    mood = mood_var.get()
    meals_eaten = meals_var.get()
    snacks = snack_var.get()
    water_consumed = water_entry.get()
    note = note_entry.get("1.0", tk.END).strip()

    if not (name and age_group and mood and meals_eaten and water_consumed.isdigit()):
        messagebox.showerror("Error", "Please fill in all required fields!")
        return

    try:
        cursor.execute(
            "INSERT INTO survey_responses (name, age_group, mood, meals_eaten, snacks, water_consumed, note) "
            "VALUES (%s, %s, %s, %s, %s, %s, %s)",
            (name, age_group, mood, meals_eaten, snacks, int(water_consumed), note)
        )
        conn.commit()
        messagebox.showinfo("Success", "Data submitted successfully!")
        root.destroy()
    except Exception as e:
        messagebox.showerror("Error", f"Data submission failed: {e}")


def open_login_page():
    global login_window, login_username_var, login_password_var

    login_window = tk.Tk()
    login_window.title("Login")
    login_window.geometry("400x300")
    login_window.config(bg="#ffebcd")

    tk.Label(login_window, text="Username:").pack(pady=10)
    login_username_var = tk.StringVar()
    tk.Entry(login_window, textvariable=login_username_var).pack(pady=5)

    tk.Label(login_window, text="Password:").pack(pady=10)
    login_password_var = tk.StringVar()
    tk.Entry(login_window, textvariable=login_password_var, show="*").pack(pady=5)

    tk.Button(login_window, text="Login", command=login_user).pack(pady=20)
    tk.Button(login_window, text="Register", command=open_registration_page).pack(pady=10)

   
    tk.Button(login_window, text="Submit", command=login_user).pack(pady=10)

    login_window.mainloop()


open_login_page()


conn.close()
