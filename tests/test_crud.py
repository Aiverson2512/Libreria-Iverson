import time

from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.select import Select
from selenium.webdriver.support.ui import WebDriverWait

from conftest import BASE_URL

MARCA = str(int(time.time()))[-6:]
TITULO = f'Libro Selenium {MARCA}'


def _esperar_url(driver, fragmento):
    WebDriverWait(driver, 5).until(EC.url_contains(fragmento))


def _clic(driver, elemento):
    driver.execute_script('arguments[0].scrollIntoView();', elemento)
    driver.execute_script('arguments[0].click();', elemento)


def _enviar_formulario(driver):
    driver.execute_script('document.querySelector("form").submit()')


def test_crear_editar_y_eliminar_libro(admin):
    admin.get(f'{BASE_URL}/admin/crear.php')

    admin.find_element(By.ID, 'titulo').send_keys(TITULO)
    admin.find_element(By.ID, 'tipo').send_keys('psychology')
    Select(admin.find_element(By.ID, 'id_pub')).select_by_value('1389')
    Select(admin.find_element(By.ID, 'autores')).select_by_value('409-56-7008')
    admin.find_element(By.ID, 'precio').send_keys('25.50')
    admin.execute_script("document.getElementById('fecha_pub').value = '2025-01-15'")
    admin.find_element(By.ID, 'notas').send_keys('Libro creado por la suite automatizada.')
    _enviar_formulario(admin)

    _esperar_url(admin, 'index.php?mensaje=Libro+creado')
    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{TITULO}')]]")
    assert TITULO in fila.text

    _clic(admin, fila.find_element(By.XPATH, ".//a[contains(@href,'editar.php')]"))
    _esperar_url(admin, 'admin/editar.php')
    precio = admin.find_element(By.ID, 'precio')
    precio.clear()
    precio.send_keys('30.99')
    _enviar_formulario(admin)

    _esperar_url(admin, 'index.php?mensaje=Libro+actualizado')
    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{TITULO}')]]")
    assert '30.99' in fila.text

    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{TITULO}')]]")
    _clic(admin, fila.find_element(By.XPATH, ".//button[contains(text(),'Eliminar')]"))
    admin.switch_to.alert.accept()
    _esperar_url(admin, 'index.php?mensaje=Libro+eliminado')

    assert TITULO not in admin.page_source


def test_crear_editar_y_eliminar_autor(admin):
    admin.get(f'{BASE_URL}/admin/autores.php')
    nombre = f'Juan{MARCA}'
    apellido = f'Prueba{MARCA}'

    admin.find_element(By.ID, 'nombre').send_keys(nombre)
    admin.find_element(By.ID, 'apellido').send_keys(apellido)
    admin.find_element(By.ID, 'telefono').send_keys('8095551234')
    admin.find_element(By.ID, 'direccion').send_keys('Calle 1')
    admin.find_element(By.ID, 'ciudad').send_keys('Santo Domingo')
    admin.find_element(By.ID, 'estado').send_keys('SD')
    admin.find_element(By.ID, 'pais').send_keys('DOM')
    admin.find_element(By.ID, 'cod_postal').send_keys('10101')
    _enviar_formulario(admin)

    _esperar_url(admin, 'autores.php')
    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{nombre} {apellido}')]]")
    assert nombre in fila.text

    _clic(admin, fila.find_element(By.XPATH, ".//a[contains(@href,'autores.php?editar=')]"))
    _esperar_url(admin, 'autores.php?editar=')
    telefono = admin.find_element(By.ID, 'telefono')
    telefono.clear()
    telefono.send_keys('8095559876')
    _enviar_formulario(admin)

    _esperar_url(admin, 'autores.php')
    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{nombre} {apellido}')]]")
    assert '8095559876' in fila.text

    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{nombre} {apellido}')]]")
    _clic(admin, fila.find_element(By.XPATH, ".//button[contains(text(),'Eliminar')]"))
    admin.switch_to.alert.accept()
    _esperar_url(admin, 'autores.php')
    assert nombre not in admin.page_source


def test_crear_editar_y_eliminar_editorial(admin):
    admin.get(f'{BASE_URL}/admin/editoriales.php')
    nombre = f'Editorial {MARCA}'

    admin.find_element(By.ID, 'nombre_pub').send_keys(nombre)
    admin.find_element(By.ID, 'ciudad').send_keys('Santiago')
    admin.find_element(By.ID, 'estado').send_keys('ST')
    _enviar_formulario(admin)

    _esperar_url(admin, 'editoriales.php')
    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{nombre}')]]")
    assert nombre in fila.text

    _clic(admin, fila.find_element(By.XPATH, ".//a[contains(@href,'editoriales.php?editar=')]"))
    _esperar_url(admin, 'editoriales.php?editar=')
    ciudad = admin.find_element(By.ID, 'ciudad')
    ciudad.clear()
    ciudad.send_keys('La Vega')
    _enviar_formulario(admin)

    _esperar_url(admin, 'editoriales.php')
    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{nombre}')]]")
    assert 'La Vega' in fila.text

    fila = admin.find_element(By.XPATH, f"//tr[.//td[contains(text(),'{nombre}')]]")
    _clic(admin, fila.find_element(By.XPATH, ".//button[contains(text(),'Eliminar')]"))
    admin.switch_to.alert.accept()
    _esperar_url(admin, 'editoriales.php')
    assert nombre not in admin.page_source


def test_no_se_puede_eliminar_autor_con_libros(admin):
    admin.get(f'{BASE_URL}/admin/autores.php')
    fila = admin.find_element(By.XPATH, "//tr[.//td[contains(text(),'Abraham Bennet')]]")
    _clic(admin, fila.find_element(By.XPATH, ".//button[contains(text(),'Eliminar')]"))
    admin.switch_to.alert.accept()
    _esperar_url(admin, 'autores.php')
    assert 'No se puede eliminar un autor que tiene libros asociados' in admin.page_source


def test_no_se_puede_eliminar_editorial_con_libros(admin):
    admin.get(f'{BASE_URL}/admin/editoriales.php')
    fila = admin.find_element(By.XPATH, "//tr[.//td[contains(text(),'Algodata Infosystems')]]")
    _clic(admin, fila.find_element(By.XPATH, ".//button[contains(text(),'Eliminar')]"))
    admin.switch_to.alert.accept()
    _esperar_url(admin, 'editoriales.php')
    assert 'No se puede eliminar una editorial que tiene libros asociados' in admin.page_source


def test_crear_libro_sin_datos_muestra_error(admin):
    admin.get(f'{BASE_URL}/admin/crear.php')
    _enviar_formulario(admin)
    assert 'El título, tipo, editorial, al menos un autor y la fecha de publicación son obligatorios' in admin.page_source


def test_login_con_campos_vacios_muestra_error(driver):
    driver.get(f'{BASE_URL}/login.php')
    _enviar_formulario(driver)
    assert 'Por favor, complete todos los campos' in driver.page_source


def test_crear_libro_con_mas_de_9_autores_rechazado(admin):
    admin.get(f'{BASE_URL}/admin/crear.php')
    admin.find_element(By.ID, 'titulo').send_keys(TITULO)
    admin.find_element(By.ID, 'tipo').send_keys('psychology')
    Select(admin.find_element(By.ID, 'id_pub')).select_by_value('1389')
    autores = Select(admin.find_element(By.ID, 'autores'))
    for indice in range(10):
        autores.select_by_index(indice)
    admin.execute_script("document.getElementById('fecha_pub').value = '2025-01-15'")
    _enviar_formulario(admin)
    assert 'Puedes asignar un máximo de 9 autores por libro' in admin.page_source


def test_crear_autor_con_codigo_postal_invalido_rechazado(admin):
    admin.get(f'{BASE_URL}/admin/autores.php')
    admin.find_element(By.ID, 'nombre').send_keys(f'Ana{MARCA}')
    admin.find_element(By.ID, 'apellido').send_keys(f'Limite{MARCA}')
    admin.find_element(By.ID, 'telefono').send_keys('8095550000')
    admin.find_element(By.ID, 'direccion').send_keys('Calle 2')
    admin.find_element(By.ID, 'ciudad').send_keys('Santiago')
    admin.find_element(By.ID, 'estado').send_keys('ST')
    admin.find_element(By.ID, 'pais').send_keys('DOM')
    admin.find_element(By.ID, 'cod_postal').send_keys('ABC')
    _enviar_formulario(admin)
    assert 'El código postal solo puede contener números' in admin.page_source
