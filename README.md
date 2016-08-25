#api文件

https://lab-eric3998.c9users.io/bank_transfer/api名稱?參數=值

###1.新增帳號
+ ex: https://lab-eric3998.c9users.io/bank_transfer/addUser.php?username=eric

+ api名稱 - addUser.php
 1. 參數1 - username(帳號)


###2.取得餘額
+ ex: https://lab-eric3998.c9users.io/bank_transfer/getBalance.php?username=eric

+ api名稱 -getBalance.php
 1. 參數1 - (string)username(帳號)


###3.轉帳
+ ex: https://lab-eric3998.c9users.io/bank_transfer/updateBalance.php?username=eric&transid=1&type=OUT&amount=100

+ api名稱 - updateBalance.php
 1. 參數1 - (string)username(帳號)
 2. 參數2 - (int)transid(轉帳序號)
 3. 參數3 - (string)type(轉帳型態) (IN,OUT)
 4. 參數4 - (int)amount(轉帳金額)


###4.轉帳確認
+ ex: https://lab-eric3998.c9users.io/bank_transfer/checkTransfer.php?username=eric&transid=5

+ api名稱 - checkTransfer.php
 1. 參數1 - (string)username(帳號)
 2. 參數2 - (int)transid(轉帳序號)