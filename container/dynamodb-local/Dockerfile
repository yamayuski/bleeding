# https://github.com/misoca/dynamodb-local-persist
FROM amazon/dynamodb-local
MAINTAINER kokuyouwind <kokuyouwind@gmail.com>

WORKDIR /home/dynamodblocal

# make database directory and change owner to dynamodb user
RUN mkdir ./db && chown -R 1000 ./db

CMD ["-jar", "DynamoDBLocal.jar", "-dbPath", "./db", "-sharedDb"]
VOLUME ["./db"]
