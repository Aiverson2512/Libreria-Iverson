from selenium.webdriver.common.by import By


BASE_URL = 'http://localhost'


def test_catalogo_se_muestra(driver):
    driver.get(f'{BASE_URL}/index.php')
    assert 'Catálogo de Libros' in driver.page_source


def test_autores_se_muestran(driver):
    driver.get(f'{BASE_URL}/autores.php')
    assert driver.find_elements(By.CSS_SELECTOR, '.card-autor')


def test_contacto_requiere_campos(driver):
    driver.get(f'{BASE_URL}/contacto.php')
    driver.find_element(By.ID, 'btnEnviar').click()
    assert driver.find_element(By.ID, 'nombre').get_attribute('required')


def test_login_rechaza_credenciales_invalidas(driver):
    driver.get(f'{BASE_URL}/login.php')
    driver.find_element(By.ID, 'usuario').send_keys('invalido')
    driver.find_element(By.ID, 'password').send_keys('invalida')
    driver.find_element(By.CSS_SELECTOR, 'button[type=submit]').click()
    assert 'Usuario o contraseña incorrectos' in driver.page_source


def test_login_permite_credenciales_validas(driver):
    driver.get(f'{BASE_URL}/login.php')
    driver.find_element(By.ID, 'usuario').send_keys('selenium_test')
    driver.find_element(By.ID, 'password').send_keys('Selenium123!')
    driver.find_element(By.CSS_SELECTOR, 'button[type=submit]').click()
    assert driver.current_url.endswith('/admin/index.php')


def test_crear_libro_exige_datos_obligatorios(driver):
    driver.get(f'{BASE_URL}/login.php')
    driver.find_element(By.ID, 'usuario').send_keys('selenium_test')
    driver.find_element(By.ID, 'password').send_keys('Selenium123!')
    driver.find_element(By.CSS_SELECTOR, 'button[type=submit]').click()
    driver.get(f'{BASE_URL}/admin/crear.php')
    assert driver.find_element(By.ID, 'titulo').get_attribute('required')
    assert driver.find_element(By.ID, 'autores').get_attribute('required')


def test_admin_protege_acceso_sin_sesion(driver):
    driver.get(f'{BASE_URL}/admin/index.php')
    assert driver.current_url.endswith('/login.php')
