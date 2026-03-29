<?php
echo password_hash('Admin@12345', PASSWORD_BCRYPT);
```

Upload to `/htdocs/public/` via FileZilla, then visit:
```
http://medistock.page.gd/hash.php