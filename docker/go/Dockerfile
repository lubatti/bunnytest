FROM golang:latest

RUN go get github.com/streadway/amqp

WORKDIR /go/src/app
COPY . .

RUN go install -v

CMD ["app"]
