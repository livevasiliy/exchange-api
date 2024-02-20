# Тестовое задание

## Задание 1

Имеется 3 таблицы

### Таблица `users`

| id  | first_name | last_name | birthday   |
| --- | ---------- | --------- | ---------- |
| 1   | Ivan       | Ivanov    | 2005-01-01 |
| 2   | Marina     | Ivanova   | 2011-03-01 |


### Таблица `books`

| id  | name               | author             |
| --- | ------------------ | ------------------ |
| 1   | Romeo and Juliet   | William Shakespeare|
| 2   | War and Peace      | Leo Tolstoy        |


### Таблица `user_books`

| id  | user_id | book_id | get_date   | return_date |
| --- | ------- | ------- | ---------- | ----------- |
| 1   | 1       | 2       | 2022-01-01 | 2022-02-01  |
| 2   | 2       | 1       | 2021-01-01 | 2022-01-01  |

Необходимо написать запрос выборки данных из представленных таблиц, который найдет и выведет всех посетителей библиотеки, 
возраст которых попадает в диапазон от 7 и до 17 лет, которые взяли две книги одного автора (взяли всего 2 книги и они одного автора), 
книги были у них в руках не более двух календарных недель (не просрочили 2-х недельный срок пользования).


###Формат вывода:
```
ID, Name (first_name  last_name), Author, Books (Book 1, Book 2, ...)
1; Ivan Ivanov; Leo Tolstoy; Book 1, Book 2
```


### Решение

```sql
SELECT 
    u.id AS ID,
    CONCAT(u.first_name, ' ', u.last_name) AS Name,
    b.author AS Author,
    GROUP_CONCAT(DISTINCT b.name ORDER BY b.id) AS Books
FROM 
    users u
JOIN 
    user_books ub1 ON u.id = ub1.user_id
JOIN 
    user_books ub2 ON u.id = ub2.user_id AND ub1.id <> ub2.id
JOIN 
    books b ON ub1.book_id = b.id AND ub2.book_id = b.id
WHERE 
    TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 7 AND 17
    AND DATEDIFF(ub1.return_date, ub1.get_date) <= 14
    AND DATEDIFF(ub2.return_date, ub2.get_date) <= 14
GROUP BY 
    u.id, b.author
HAVING 
    COUNT(DISTINCT ub1.book_id) = 2
ORDER BY 
    u.id;

```

## Задание 2

Необходимо реализовать JSON API сервис на  языке php 8 (можно использовать любой php framework) для работы с курсами обмена валют для биткоина (BTC). Реализовать необходимо с помощью Docker.

Сервис для получения текущих курсов валют: https://blockchain.info/ticker

Все методы API будут доступны только после авторизации, т.е. все методы должны быть по умолчанию не доступны и отдавать ошибку авторизации.

Для авторизации будет использоваться фиксированный токен (64 символа включающих в себя a-z A-Z 0-9 а так-же символы - и _ ), передавать его будем в заголовках запросов. Тип Authorization: Bearer.

Формат запросов: ```<your_domain>/api/v1?method=<method_name>&<parameter>=<value>```

Формат ответа API: JSON (все ответы при любых сценариях должны иметь JSON формат)

Все значения курса обмена должны считаться учитывая нашу комиссию = 2%

API должен иметь 2 метода:

**rates**: Получение всех курсов с учетом комиссии = 2% (GET запрос) в формате:
```json
{
  "status": "success",
  "code": 200,
  "data": {
    "USD": <rate>,
    ...
  }
}
```

В случае ошибки:
```json
{
  "status": "error",
  "code": 403,
  "message": "Invalid token"
}
```
Сортировка от меньшего курса к большему курсу.

В качестве параметров может передаваться интересующая валюта, в формате USD,RUB,EUR и тп В этом случае, отдаем указанные в качестве параметра currency значения.

**convert**: Запрос на обмен валюты c учетом комиссии = 2%. POST запрос с параметрами:
```
currency_from: USD
currency_to: BTC
value: 1.00
```

или в обратную сторону
```
currency_from: BTC
currency_to: USD
value: 1.00
```

В случае успешного запроса, отдаем:
```json
{
  "status": "success",
  "code": 200,
  "data": {
    "currency_from": "BTC",
    "currency_to": "USD",
    "value": 1.00,
    "converted_value": 1.00,
    "rate": 1.00
  }
}
```

В случае ошибки:
```json
{
  "status": "error",
  "code": 403,
  "message": "Invalid token"
}
```

Важно, минимальный обмен равен 0,01 валюты from
Например: USD = 0.01 меняется на 0.0000005556 (считаем до 10 знаков)
Если идет обмен из BTC в USD - округляем до 0.01

## Решение

## Требования
- Docker
- Docker Compose

## Использование

### 1. Запуск Docker Контейнеров

Для запуска Docker контейнеров (PHP, MySQL, Redis, Nginx), выполните следующую команду:

```bash
make start
```
### 2. Установка Зависимостей
Установите зависимости проекта с использованием Composer:

```bash
make install
```

### 3. Запуск Миграций
Выполните миграции базы данных Laravel:

```bash
make migrate
```

### 4. Наполнение Базы Данных (Опционально)
Если необходимо, наполните базу данных тестовыми данными:

```bash
make seed
```

### 5. Запуск PHPUnit Тестов
Запустите тесты PHPUnit:

```bash
make test
```

### 6. Остановка Docker Контейнеров
Для остановки Docker контейнеров:

```bash
make stop
```

### 7. Удаление Docker Контейнеров, Сетей и Томов
Для полной остановки и удаления Docker контейнеров, сетей и томов:

```bash
make down
```

### 8. Просмотр Логов Docker Контейнеров
Для просмотра логов Docker контейнеров:

```bash
make logs
```

## Дополнительные Команды

### Обновление Зависимостей:
Это обновит зависимости проекта.
```bash
make update
```

### Линтинг:
```bash
make lint
```

### Помощь
Чтобы посмотреть список всех доступных команд.
```bash
make help
```

## Тестирование

В папке `docs` хранится готовая Postman коллекция
подготовленных запросов.
