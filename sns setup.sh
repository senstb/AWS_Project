#!/bin/bash

ARN = ('aws sns create-topic --name mproject2')

echo "This is ARN: $ARN"

aws sns set-topic-attributes --topic-arn $ARN --attribute-name DisplayName--attribute-value mproject2

aws sns subscribe --topic-arn $ARN --protocol email --notification-endpoint bsenst@hawk.iit.edu

