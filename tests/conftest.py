from pathlib import Path

import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait

BASE_URL = 'http://localhost'
USUARIO = 'selenium_test'
CONTRASENA = 'Selenium123!'


@pytest.fixture
def driver():
    options = Options()
    options.add_argument('--headless=new')
    options.add_argument('--window-size=1440,1000')
    driver = webdriver.Chrome(options=options)
    driver.implicitly_wait(3)
    yield driver
    driver.quit()


@pytest.fixture
def admin(driver):
    driver.get(f'{BASE_URL}/login.php')
    driver.find_element(By.ID, 'usuario').send_keys(USUARIO)
    driver.find_element(By.ID, 'password').send_keys(CONTRASENA)
    driver.find_element(By.CSS_SELECTOR, 'button[type=submit]').click()
    WebDriverWait(driver, 5).until(EC.url_contains('/admin/index.php'))
    return driver


@pytest.hookimpl(hookwrapper=True)
def pytest_runtest_makereport(item, call):
    outcome = yield
    report = outcome.get_result()
    if report.when != 'call':
        return
    driver = item.funcargs.get('driver')
    if driver:
        directory = Path('reports/screenshots')
        directory.mkdir(parents=True, exist_ok=True)
        driver.save_screenshot(str(directory / f'{item.name}.png'))
