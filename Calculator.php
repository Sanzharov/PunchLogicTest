<?php


class Calculator
{
    public function execute()
    {
        $json = file_get_contents('data.json', './data.json');
        // Decode the JSON string into a PHP array
        $data = json_decode($json, true);

        $jobRates = [];
        // Loop through each job in the $jobMeta array
        foreach ($data['jobMeta'] as $job) {
            // Get the job title, hourly wage rate, and benefits accrual rate
            $jobTitle = $job['job'];
            $hourlyWageRate = $job['rate'];
            $benefitsAccrualRate = $job['benefitsRate'];

            $jobRates[$jobTitle] = [
                'rate' => $hourlyWageRate,
                'benefit' => $benefitsAccrualRate
            ];
        }

        $result = [];
        // Loop through each employee's data
        foreach ($data['employeeData'] as $employeeData) {
            $employeeName = $employeeData['employee'];
            // Initialize variables to store totals
            $totalRegularHours = 0;
            $totalOvertimeHours = 0;
            $totalDoubletimeHours = 0;
            $totalWage = 0;
            $totalBenefits = 0;
            $totalHours = 0;
            // Loop through each time punch for the current employee
            foreach ($employeeData['timePunch'] as $timePunch) {
                // Get the job information (rate and benefit) for the current time punch
                $job = $timePunch['job'];
                $rate = $jobRates[$job]['rate'];
                $jobBenefit = $jobRates[$job]['benefit'];
                // Calculate the hours worked for the current time punch
                $startTime = strtotime($timePunch['start']);
                $endTime = strtotime($timePunch['end']);
                $hours = ($endTime - $startTime) / 3600;
                // Calculate the wage for the current time punch based on the hours worked and the job rate
                if ($totalHours + $hours <= 40) {
                    // If the total hours worked for the week are less than or equal to 40, the current time punch is regular time
                    $totalWage += $hours * $rate;
                    $totalRegularHours += $hours;
                } elseif ($totalHours + $hours < 48) {
                    // If the total hours worked for the week are less than 48 but more than 40, the current time punch is overtime
                    $regularHours = 40 - $totalHours;
                    $totalWage += $regularHours * $rate;
                    $totalRegularHours += $regularHours;

                    $overtimeHours = $hours - $regularHours;;
                    $totalOvertimeHours += $overtimeHours;
                    $overtimePay = $overtimeHours * ($rate * 1.5);
                    $totalWage += $overtimePay;
                } elseif ($totalHours + $hours > 48) {
                    // If the total hours worked for the week are more than 48, the current time punch is double time
                    $regularHours = 48 - $totalHours;
                    $overtimeHours = $regularHours;
                    $totalOvertimeHours += $overtimeHours;
                    $overtimePay = $overtimeHours * $rate * 1.5;
                    $totalWage += $overtimePay;

                    $doubletimePay = ($hours - $regularHours) * $rate * 2;
                    $totalDoubletimeHours += $hours - $regularHours;
                    $totalWage += $doubletimePay;
                }
                $totalHours += $hours;
                $benefits = $hours * $jobBenefit;
                $totalBenefits += $benefits;
            }

            $result[$employeeName] = [
                "employee" => $employeeName,
                "regular" => sprintf("%.4f", $totalRegularHours),
                "overtime" => sprintf("%.4f", $totalOvertimeHours),
                "doubletime" => sprintf("%.4f", $totalDoubletimeHours),
                "wageTotal" => sprintf("%.4f", $totalWage),
                "benefitTotal" => sprintf("%.4f", $totalBenefits)

            ];
        }
        // Encode the modified data back into a JSON string
        $data = json_encode($result, JSON_PRETTY_PRINT);
        // Save the modified JSON string back into the file
        file_put_contents("result.json", $data);
    }
}