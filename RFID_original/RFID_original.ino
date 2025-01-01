#include <WiFi.h>
#include <SPI.h>
#include <MFRC522.h>
#include <HTTPClient.h>

//************************************************************************
#define SS_PIN  19  // Chân SS
#define RST_PIN 10  // Chân RST
#define BUZZER_PIN 3 // Chân GPIO điều khiển còi
//************************************************************************
MFRC522 mfrc522(SS_PIN, RST_PIN); // Tạo instance MFRC522
//************************************************************************
const char *ssid = "PHONG 305";
const char *password = "0349277602";
const char* device_token = "390140bc777d78c8";

String URL = "http://192.168.1.4/rfidio/getdata.php"; // IP server
String oldCardID = "";


void setup() {
  Serial.begin(115200);
  SPI.begin(6, 0, 4);  // SPI (sck, miso, mosi, SS)
  connectToWiFi();

  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Wi-Fi connection failed! Check credentials.");
    while (1);  // Dừng nếu không kết nối
  }

  mfrc522.PCD_Init();          // Khởi tạo module RFID
  pinMode(BUZZER_PIN, OUTPUT); // Thiết lập chân còi là OUTPUT
  digitalWrite(BUZZER_PIN, LOW);
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectToWiFi();
  }


  if (!mfrc522.PICC_IsNewCardPresent()) {
    return; // Không có thẻ
  }

  if (!mfrc522.PICC_ReadCardSerial()) {
    return; // Đọc thẻ lỗi
  }

  String cardID = getCardID();
  Serial.print("Card ID: ");
  Serial.println(cardID);
  sendCardID(cardID);
}

String getCardID() {
  char cardID[20];
  sprintf(cardID, "%02X%02X%02X%02X", mfrc522.uid.uidByte[0], mfrc522.uid.uidByte[1], 
          mfrc522.uid.uidByte[2], mfrc522.uid.uidByte[3]);
  return String(cardID);
}

void sendCardID(String card_uid) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String getData = "?card_uid=" + card_uid + "&device_token=" + device_token;
    String link = URL + getData;
    Serial.print("Sending to: ");
    Serial.println(link);
    
    http.begin(link);  // Initiate HTTP request
    int httpCode = http.GET();  // Perform GET request
    
    if (httpCode > 0) {  // Check HTTP code
      String payload = http.getString();  // Get server response
      Serial.println("Server response: " + payload);

      // Process server response
      if (httpCode == HTTP_CODE_OK) {
        if (payload.indexOf("Login") >= 0 || 
            payload.indexOf("Logout") >= 0 || 
            payload.indexOf("New card successfully registered!") >= 0) 
            {  // Buzzer beep 3 times
            digitalWrite(BUZZER_PIN, HIGH);
            delay(200);
            digitalWrite(BUZZER_PIN, LOW);
            delay(200);
        }
      } else {
        Serial.println("Error: Server returned status " + String(httpCode));
      }
    } else {
      Serial.println("HTTP request failed: " + http.errorToString(httpCode));
    }
    http.end();  // Free resources
  } else {
    Serial.println("Wi-Fi not connected.");
  }
}


void connectToWiFi() {
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nConnected to Wi-Fi");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}
