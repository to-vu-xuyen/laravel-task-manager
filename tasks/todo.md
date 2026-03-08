# Task: Phân bổ cấu trúc thư mục Admin và User

## Mục tiêu
Thiết kế cấu trúc thư mục chuẩn best practice (tuân thủ SOLID, DDD, Clean Architecture) để tách biệt Admin và User.

## Checklist
- [ ] Nghiên cứu và xác định cấu trúc module (Domain vs Http).
- [ ] Tách biệt Presentation Layer (Controllers, Requests) cho Admin và User (Web).
- [ ] Dùng chung Domain Layer (Services, Repositories, Models) để tránh lặp code.
- [ ] Xác nhận kiến trúc với User.
- [ ] (Tuỳ chọn) Scaffold cấu trúc thư mục và file cơ bản nếu được yêu cầu.

## Review
- Đảm bảo tuân thủ nguyên lý Single Responsibility Principle (Tách HTTP handling ra khỏi Business Logic).
- Đảm bảo tuân thủ Open/Closed Principle (Thêm role mới không làm bung bét code cũ hiện tại).
- Đảm bảo Dependency Inversion Principle (Controller gọi interface của Service).
