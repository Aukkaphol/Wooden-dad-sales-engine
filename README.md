# Wooden Dad Sales Engine

Laravel 12 sales landing system for Wooden Dad Design, focused on Bedroom Set lead capture.

## Pages

- `/` หน้า Home landing page
- `/bedroom-set` หน้าแพ็กเกจ Bedroom Set
- `/lead` ฟอร์มขอราคาและแบบฟรี
- `/thank-you` หน้าขอบคุณหลังส่งฟอร์ม
- `/admin/leads` หน้าแอดมินสำหรับดูรายการลูกค้า ต้องเข้าสู่ระบบ

## Admin Dashboard

- ค้นหาด้วยชื่อหรือเบอร์โทร
- กรองตามจังหวัด สถานะ และช่วงวันที่
- CRM Pipeline status: `new`, `contacted`, `designing`, `quoted`, `deposit_paid`, `production`, `installation`, `completed`, `lost`
- หน้า Lead detail สำหรับดูข้อมูลลูกค้า รูปห้อง และบันทึก admin notes
- Notes timeline สำหรับบันทึกประวัติการคุยและการอัปเดตสถานะ
- Dashboard cards: New Leads, Contacted, Quoted, Deposit Paid, Completed
- Dashboard charts: Leads by month และ Conversion rate
- Sprint 3 widgets: Leads Today, Leads This Month, Pending Follow-up
- Drag and drop Kanban cards สำหรับย้าย Pipeline status
- Quotation status และ Follow-up date ในหน้า Lead detail
- Sprint 4 quotation module: auto quotation numbers, quotation items, subtotal calculation, PDF quotation, and quotation history linked to leads
- Sprint 5 production queue: Approved quotations automatically create production orders, production Kanban stages, and multiple craftsman assignment
- Export ข้อมูลเป็นไฟล์ `.xls` ที่เปิดด้วย Excel ได้

## LINE OA Notification

เมื่อลูกค้าส่งฟอร์ม Lead ใหม่ ระบบจะบันทึกข้อมูลก่อน แล้วค่อยส่งข้อความไปที่ LINE OA ผ่าน LINE Messaging API ถ้าไม่ได้ตั้งค่า token หรือส่งไม่สำเร็จ ระบบจะ log warning และยัง redirect ไปหน้า `/thank-you` ตามปกติ

เพิ่มค่าเหล่านี้ใน `.env`:

```dotenv
LINE_CHANNEL_ACCESS_TOKEN=ใส่ Channel access token จาก LINE Developers
LINE_USER_ID=ใส่ userId ของแอดมิน ถ้าต้องการ push หาแอดมินคนเดียว
LINE_GROUP_ID=ใส่ groupId ถ้าต้องการ push เข้ากลุ่มแอดมิน
APP_URL=https://woodendaddesign.com
LINE_ADMIN_BASE_URL=${APP_URL}
```

การส่งข้อความจะเลือก `LINE_GROUP_ID` ก่อน ถ้าไม่มีจะใช้ `LINE_USER_ID` ถ้าไม่มีทั้งคู่ระบบจะใช้ broadcast message ไปยังผู้ติดตาม LINE OA แทน

ตัวอย่างข้อความ:

```text
🔥 Lead ใหม่ Wooden Dad Design

ชื่อ:
เบอร์:
จังหวัด:
งบประมาณ:
ขนาดห้อง:
ข้อความ:

ดูรายละเอียด:
https://woodendaddesign.com/admin/leads/{id}
```

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
php artisan serve
```

ตั้งค่า MySQL ใน `.env`:

```dotenv
DB_DATABASE=wooden_dad_sales_engine
DB_USERNAME=root
DB_PASSWORD=
```

บัญชีแอดมินเริ่มต้นหลัง seed:

```text
email: admin@woodendad.local
password: password
```
