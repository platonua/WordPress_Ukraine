# Модуль WordPress для Украины

## Установка:

* Установка производится из маркетплейса Wordpress: Plugins -> Добавить новый -> Выполнить поиск «Platon Pay» -> Установить.

* После успешной установки Вам необходимо «Активировать» ваш плагин (Plugins -> Platon Pay -> Активировать).

* В боковом меню админ консоли выбираем «Platon Pay».

* Вы можете установить галочку «Тестовый режим» и провести тестирование работы модуля (в тестовом режиме модуль позволяем полностью проверить процесс оплаты на сайте, и посмотреть, как клиенты будут оплачивать ваши товары на сайте).

* Для дальнейших настроек Вам необходимо снять галочку «Тестовый режим» и нажать кнопку «Подключить PSP Platon».

* Заполнить поля «Секретный ключ» и «Пароль» — полученные у менеджера.

* По желанию Вы можете заполнить поле «Return url» — указав страницу, на которую будет перенаправлен пользователь после успешной оплаты.

* Сохранить изменения.

## Ссылка для коллбеков:
https://ВАШ_САЙТ/?platon-result=Result_Payment

## Тестирование:
В целях тестирования используйте наши тестовые реквизиты.

| Номер карты  | Месяц / Год | CVV2 | Описание результата |
| :---:  | :---:  | :---:  | --- |
| 4111  1111  1111  1111 | 02 / 2022 | Любые три цифры | Не успешная оплата без 3DS проверки |
| 4111  1111  1111  1111 | 06 / 2022 | Любые три цифры | Не успешная оплата с 3DS проверкой |
| 4111  1111  1111  1111 | 01 / 2022 | Любые три цифры | Успешная оплата без 3DS проверки |
| 4111  1111  1111  1111 | 05 / 2022 | Любые три цифры | Успешная оплата с 3DS проверкой |
