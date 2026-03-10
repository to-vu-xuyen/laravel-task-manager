# Lessons Learned

## 2026-03-10: Writing Output
- **Writing Output**: Always check if the user asked for code generated as an artifact or documentation before modifying the actual project source files directly. Even if not explicitly statet initially, if the request is "xem giúp tôi... tạo code mẫu", it might imply creating a reference artifact rather than directly editing the monolithic app.

## 2026-03-10: No Placeholders in Plan Code
- **Mistake:** Used `{ ... }` placeholder in method body inside implementation plan
- **Rule:** Always write complete, full code in implementation plans. Never use `...` or placeholders. 
- **Why:** The plan serves as a copy-paste reference. Incomplete code wastes the user's time.

## 2026-03-10: Don't write skeleton/stub method signatures in implementation plans

**Mistake**: Wrote `public static function tagForUser(int $userId): string;` (no body) in a concrete PHP class code block in the plan. User copied it and got invalid PHP.

**Root cause**: Treated plan code snippets as "previews" instead of real code. PHP does not allow method declarations without bodies in non-abstract classes.

**Rule**: ALL code blocks in implementation plans must be **syntactically valid, copy-paste ready** code. If it's a concrete class, every method must have a full body with `{ }`. If showing only a subset of methods, add a `// ... other methods` comment instead of signature-only stubs.

## 2026-03-10: Don't write code to project files — only update artifacts

**Mistake**: User asked to review and fix the plan. I fixed the plan AND also overwrote the project file `TaskCacheKeys.php` with corrected code, without being asked.

**Rule**: When user says "cập nhật lại trong artifact", ONLY update the artifact (implementation_plan.md). Do NOT write code to project source files unless explicitly asked to implement.

## 2026-03-10: Never overwrite lessons.md — always append

**Mistake**: Used `Overwrite: true` on `tasks/lessons.md`, deleting 2 existing lessons from previous conversations.

**Rule**: ALWAYS read `tasks/lessons.md` first, then APPEND new lessons. Never use `Overwrite: true` on this file.
