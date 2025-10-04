from playwright.sync_api import Page, expect, sync_playwright

def take_screenshot(page: Page, url: str, file_name: str, heading_text: str):
    """Navigates to a URL, verifies a heading, and takes a screenshot."""
    page.goto(url)
    heading = page.get_by_role("heading", name=heading_text)
    expect(heading).to_be_visible()
    page.screenshot(path=f"jules-scratch/verification/{file_name}")

def main():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        base_url = "http://127.0.0.1:8000"

        # Verify landing page
        take_screenshot(page, base_url, "landing_page.png", "Welcome to the Student Management System")

        # Verify Admin login page
        take_screenshot(page, f"{base_url}/admin/", "admin_login.png", "Admin Login")

        # Verify Teacher login page
        take_screenshot(page, f"{base_url}/teacher/", "teacher_login.png", "Teacher Login")

        # Verify Parent login page
        take_screenshot(page, f"{base_url}/parent/", "parent_login.png", "Parent Login")

        # Verify Student login page
        take_screenshot(page, f"{base_url}/student/", "student_login.png", "Student Login")

        browser.close()

if __name__ == "__main__":
    main()