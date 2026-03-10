# Lessons Learned

## 2026-03-10: Writing Output
- **Writing Output**: Always check if the user asked for code generated as an artifact or documentation before modifying the actual project source files directly. Even if not explicitly statet initially, if the request is "xem giúp tôi... tạo code mẫu", it might imply creating a reference artifact rather than directly editing the monolithic app.

## 2026-03-10: No Placeholders in Plan Code
- **Mistake:** Used `{ ... }` placeholder in method body inside implementation plan
- **Rule:** Always write complete, full code in implementation plans. Never use `...` or placeholders. 
- **Why:** The plan serves as a copy-paste reference. Incomplete code wastes the user's time.
