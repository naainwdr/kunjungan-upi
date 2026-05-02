# Database ERD

## Overview

This project currently uses a single main domain table for visit reservations:
- `kunjungan`

There is also a standard `users` table used for admin authentication, but it is not currently related by foreign key to `kunjungan`.

## Entity Relationship Diagram

```mermaid
erDiagram
    KUNJUNGAN {
        bigint id PK
        varchar nomor_registrasi UQ
        varchar nama_sekolah
        varchar npsn
        text alamat
        varchar nama_pic
        varchar jenis_pic
        varchar email_pic
        varchar telepon_pic
        varchar email
        varchar telepon
        date tanggal_kunjungan
        varchar jam_mulai
        varchar jam_selesai
        int jumlah_peserta
        int jumlah_kepsek
        int jumlah_guru
        int jumlah_tendik
        varchar file_surat
        varchar status
        text catatan_admin
        timestamp email_notified_at
        timestamp created_at
        timestamp updated_at
    }
```

## Notes

- `nomor_registrasi` is unique and used as the public reference for lookup.
- `status` currently includes values such as `pending`, `approved`, `rejected`, `cancelled`, and `completed`.
- `email_pic` and `telepon_pic` are the PIC contact fields used for evaluation notifications.
- There is no relational foreign key from `kunjungan` to another domain table in the current schema.

## How to use

- Use this ERD to understand the reservation data model.
- The `kunjungan` table stores all reservation details, PIC contact info, participant counts, approval status, and notification timestamps.
- For diagrams, use a Mermaid-compatible viewer or the rendered markdown in VS Code.
