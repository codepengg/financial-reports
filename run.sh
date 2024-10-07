#!/bin/sh

# Cek apakah variabel PASSWORD sama dengan "gatau"
if [ "$PASSWORD" = "gatau" ]; then
    echo "Password benar, menjalankan Laravel..."

    # Menjalankan Laravel pada port 8000
    php artisan serve --host=0.0.0.0 --port=8000
else
    echo "Password salah!"
    exit 1
fi
