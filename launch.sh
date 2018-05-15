#!/bin/bash


declare -a instArray

./cleanup.sh
./launch-rds.sh 

mapfile -t instArray < <(aws ec2 run-instances --image-id $1 --count $2 --instance-type $3  --security-group-ids $4 --subnet-id $5 --key-name $6 --iam-instance-profile Name=$7 --associate-public-ip-address --user-data C://users/braden/desktop/miniproject/install-webserver.sh --output table | grep InstanceId | sed "s/|//g" | tr -d ' '| sed "s/InstanceId//g") 

echo ${instArray[@]}

aws ec2 wait instance-running --instance-ids ${instArray[@]}

echo ${instArray[@]}
echo "instance are running"

aws elb create-load-balancer --load-balancer-name load --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --security-groups sg-ee7bf888 --subnets subnet-d5a25ee8 --output=text
ELBURL=('aws elb create-load-balancer --load-balancer-name load --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --security-groups sg-f647b490 --subnets subnet-3d0b2816 --output=text'); 
	
	echo -e "\nFinished launching ELB and sleeping 25 seconds"
	for i in {0..25}; do echo -ne '.'; sleep 1;done
	echo "\n"
	
aws elb register-instances-with-load-balancer --load-balancer-name load --instances ${instArray[@]}
aws elb configure-health-check --load-balancer-name load --health-check Target=HTTP:80/index.html,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3

aws autoscaling create-launch-configuration --launch-configuration-name itmo444-launch-config --image-id $1 --security-groups $4 --instance-type $3 --key-name $6 --iam-instance-profile $7 --user-data C://users/braden/desktop/miniproject/install-webserver.sh 

aws autoscaling create-auto-scaling-group --auto-scaling-group-name itmo-444-extended-auto-scaling-group-2 --launch-configuration-name itmo444-launch-config --load-balancer-names lb  --health-check-type ELB --min-size 3 --max-size 6 --desired-capacity 3 --default-cooldown 600 --health-check-grace-period 120 --vpc-zone-identifier $5


