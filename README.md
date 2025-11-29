# Pokemon API

Serwis umożliwia pobieranie informacji o Pokemonach z PokeAPI oraz zarządzanie własnymi i zakazanymi Pokemonami. Projekt stworzony w Laravel 12 z REST API, cache i prostym systemem autoryzacji.

---

## 📦 Wymagania

- PHP >= 8.2
- Composer
- Laravel 12
- Redis (do cache)

---

## ⚡ Instalacja

1. Sklonuj repozytorium:

```bash
git clone <repo-url>
cd pokemon-arrmy
```

2. Zainstaluj zależności:

```bash
composer install
```

3. Skopiuj plik `.env` i wygeneruj klucz:

```bash
cp .env.example .env
php artisan key:generate
```

4. Ustaw w `.env`:

```env
SUPER_SECRET_KEY=123456
POKEAPI_URL=https://pokeapi.co/api/v2
MYSQL WG siebie
Oraz redis na phpredis
```

5. Wykonaj migracje:

```bash
php artisan migrate
```

6. Uruchom serwer lokalny:

```bash
php artisan serve
```

Domyślny URL: `http://127.0.0.1:8000`

---

## 🗂 Routing i Endpointy

### Middleware `secret`

Ścieżki chronione wymagają nagłówka:

```
X-SUPER-SECRET-KEY: 123456
```

---

### 1️⃣ Banned Pokemony

| Metoda | Endpoint | Headers | Body | Opis |
|--------|---------|--------|------|-----|
| GET | `/api/banned` | `X-SUPER-SECRET-KEY` | brak | Pobiera listę wszystkich zakazanych Pokemonów |
| POST | `/api/banned` | `X-SUPER-SECRET-KEY` | `{"name":"pikachu"}` | Dodaje nowego zakazanego Pokemona |
| DELETE | `/api/banned/{name}` | `X-SUPER-SECRET-KEY` | brak | Usuwa Pokemona z listy zakazanych |

---

### 2️⃣ Custom Pokemony (CRUD)

| Metoda | Endpoint | Headers | Body | Opis |
|--------|---------|--------|------|-----|
| GET | `/api/custom` | `X-SUPER-SECRET-KEY` | brak | Pobiera wszystkie własne Pokemony |
| POST | `/api/custom` | `X-SUPER-SECRET-KEY` | `{"name":"myCustomMon","data":{"type":"fire","attack":50}}` | Dodaje nowego Pokemona |
| GET | `/api/custom/{id}` | `X-SUPER-SECRET-KEY` | brak | Pobiera konkretnego Pokemona po ID |
| PUT | `/api/custom/{id}` | `X-SUPER-SECRET-KEY` | `{"name":"myCustomMon","data":{"type":"fire","attack":70}}` | Aktualizuje Pokemona |
| DELETE | `/api/custom/{id}` | `X-SUPER-SECRET-KEY` | brak | Usuwa Pokemona po ID |

---

### 3️⃣ Info o Pokemonach (publiczne)

- **POST** `/api/info`  
  Pobiera informacje o liście Pokemonów (oficjalnych z PokeAPI oraz własnych).

**Body JSON przykład:**

```json
{
    "names": ["pikachu", "myCustomMon", "charizard"]
}
```

**Response JSON przykład:**

```json
[
  {
    "name":"pikachu",
    "data": { /* dane z PokeAPI */ },
    "source":"official"
  },
  {
    "name":"myCustomMon",
    "data":{"type":"fire","attack":50},
    "source":"custom"
  }
]
```

> Zakazane Pokemony są pomijane.

---

## 🗄 Cache

- Dane z PokeAPI są cache’owane do **12:00 UTC+1 następnego dnia**.
- Cache można zmienić w `.env` ustawiając `CACHE_STORE` (file, redis itp.).

---

## 🔒 Autoryzacja

- Endpointy `/banned` i `/custom` wymagają nagłówka:

```
X-SUPER-SECRET-KEY: 123456
```

- `/info` jest publiczne i nie wymaga autoryzacji.

---

## 🛠 Testowanie w Postman

### Banned Pokemony

**GET /api/banned**
- Headers: `X-SUPER-SECRET-KEY: 123456`

**POST /api/banned**
- Headers: `X-SUPER-SECRET-KEY: 123456`
- Body:
```json
{
  "name": "charizard"
}
```

**DELETE /api/banned/charizard**
- Headers: `X-SUPER-SECRET-KEY: 123456`

### Custom Pokemony

**POST /api/custom**
- Headers: `X-SUPER-SECRET-KEY: 123456`
- Body:
```json
{
  "name": "myCustomMon",
  "data": {"type":"fire","attack":50}
}
```

**PUT /api/custom/1**
- Headers: `X-SUPER-SECRET-KEY: 123456`
- Body:
```json
{
  "name": "myCustomMon",
  "data": {"type":"fire","attack":70}
}
```

**DELETE /api/custom/1**
- Headers: `X-SUPER-SECRET-KEY: 123456`

### Info Pokemony

**POST /api/info**
- Body:
```json
{
  "names": ["pikachu", "myCustomMon"]
}
```

**Response:**
```json
[
  {"name":"pikachu","data":{/* dane */},"source":"official"},
  {"name":"myCustomMon","data":{"type":"fire","attack":50},"source":"custom"}
]
```

---

## 💡 Uwagi

- Własne Pokemony nie mogą mieć tej samej nazwy co istniejące w PokeAPI.  
- Zakazane Pokemony nie są zwracane w `/info`.  
