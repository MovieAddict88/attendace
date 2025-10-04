from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Go to the application
        page.goto("http://localhost:8000")

        # Click the "Add New Student" button
        page.click("a[href='add.php']")

        # Fill out the form
        page.fill("input[name='name']", "John Doe")
        page.fill("input[name='email']", "john.doe@example.com")
        page.fill("input[name='phone']", "1234567890")

        # Submit the form
        page.click("input[type='submit']")

        # Wait for navigation to the main page
        page.wait_for_url("http://localhost:8000/index.php")

        # Take a screenshot
        page.screenshot(path="jules-scratch/verification/verification.png")

        print("Verification script ran successfully.")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="jules-scratch/verification/error.png")

    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)