---
title: log_once()
---

```php
Debug::log_once (string $message)
```

Logs a debug message if the debugger is enabled. Does nothing if the debugger is disabled. Each message is logged only once (based on file and line number).