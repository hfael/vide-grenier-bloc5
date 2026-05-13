from pathlib import Path
from time import time
from playwright.sync_api import sync_playwright


BASE_URL = "http://localhost:8080"
ASSET_DIR = Path("docs/assets")


def screenshot(page, path):
    page.screenshot(path=str(path), full_page=False)


def main():
    ASSET_DIR.mkdir(parents=True, exist_ok=True)

    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page(viewport={"width": 1365, "height": 900})

        page.goto(BASE_URL + "/", wait_until="networkidle")
        screenshot(page, ASSET_DIR / "accueil.png")

        email = "doc-user-%d@example.test" % int(time())
        page.goto(BASE_URL + "/register", wait_until="networkidle")
        page.fill("#username", "Utilisateur documentation")
        page.fill("#exampleInputEmail1", email)
        page.fill("#exampleInputPassword1", "Password123!")
        page.fill("#exampleInputPassword2", "Password123!")
        screenshot(page, ASSET_DIR / "inscription.png")
        page.click("button[name='submit']")
        page.wait_for_url("**/account", timeout=15000)

        page.goto(BASE_URL + "/product", wait_until="networkidle")
        screenshot(page, ASSET_DIR / "depot-annonce.png")

        page.goto(BASE_URL + "/product/1", wait_until="networkidle")
        page.fill("#contact-name", "Utilisateur documentation")
        page.fill("#contact-email", email)
        page.fill("#contact-message", "Bonjour, cette annonce est-elle toujours disponible ?")
        page.click("button[name='contact_submit']")
        page.wait_for_load_state("networkidle")
        screenshot(page, ASSET_DIR / "contact-confirmation.png")

        browser.close()


if __name__ == "__main__":
    main()
