# PunchLogicTest

To run a function in a PHP class from the console.


1. Open a terminal or command prompt and navigate to the directory where your PHP file is located.

2. Start the PHP console by typing `"php -a"` and pressing Enter.

3. Require your PHP file that contains the class definition using the `"require"` or `"include"` statement. 

For example:

`include('Calculator.php');`

4. Create an instance of the class using the "new" keyword. 

For example:

`$calculator = new Calculator();`
5. Call the function.

For example:

`$calculator->execute();`