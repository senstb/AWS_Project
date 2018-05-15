#!/bin/bash


mapfile -t dbInstanceARR < <(aws rds describe-db-instances --output json | grep "\"DBInstanceIdentifier" | sed "s/[\"\:\, ]//g" | sed "s/DBInstanceIdentifier//g" )

echo "in launch rds"
echo ${dbInstanceARR[@]}
if [ ${#dbInstanceARR[@]} -gt 0 ]
   then
   
   LENGTH=${#dbInstanceARR[@]}

      for (( i=0; i<${LENGTH}; i++));
      do
      if [ ${dbInstanceARR[i]} == "mp1-rca" ] 
     then 
      echo "db exists"
     
    else
     echo "in launch rds2"
     aws rds create-db-instance --db-instance-identifier mp1-brs --db-instance-class db.t1.micro --engine MySQL --master-username bsenst --master-user-password josy93! --allocated-storage 5 --db-subnet-group-name subnetgp --db-name itm444mp
      fi  
     done
fi

if [ ${#dbInstanceARR[@]} == 0 ]
then
echo "in launch rds3"
 aws rds create-db-instance --db-instance-identifier mp1-brs --db-instance-class db.t1.micro --engine MySQL --master-username bsenst --master-user-password josy93! --allocated-storage 5 --db-subnet-group-name subnetgp --db-name itm444mp
fi

