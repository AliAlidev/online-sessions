const dbName = "OnlineEventSharingDB";
const dbVersion = 1;
let db;
const fingerprint = getFingerPrint();

export async function initialization() {
    db = await initializeDB();

    const transaction = db.transaction("authentications", "readonly");
    const store = transaction.objectStore("authentications");
    const getRequest = store.get(fingerprint);
    getRequest.onsuccess = async function () {
        if (getRequest.result !== undefined) {
        } else {
            var token = await fetchAuthTokenFromServer();
            let dataObject = {
                fingerprint: fingerprint,
                token: token
            };
            saveData(dataObject);
        }
    };


}

// Initialize the IndexedDB
async function initializeDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(dbName, dbVersion);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains("authentications")) {
                const objectStore = db.createObjectStore("authentications", {
                    keyPath: "fingerprint",
                    primaryKey: "fingerprint"
                });
                objectStore.createIndex("token", "token", {
                    unique: true
                });
            }
        };

        request.onsuccess = (event) => {
            resolve(event.target.result); // Resolve with the db when initialization is complete
        };

        request.onerror = (event) => {
            reject(event.target.error); // Reject if there's an error
        };
    });
}

// Save data to the database
function saveData(data) {
    const transaction = db.transaction("authentications", "readwrite");
    const store = transaction.objectStore("authentications");
    let getRequest = store.get(data.fingerprint);
    getRequest.onsuccess = function () {
        if (getRequest.result === undefined) {
            store.add(data);
        }
    };

    transaction.onsuccess = () => {
        console.log("Data saved successfully");
    };

    transaction.onerror = (event) => {
        console.error("Error saving data:", event.target.error);
    };
}

async function fetchAuthTokenFromServer() {
    if (!await getUserToken()) {
        return new Promise((resolve, reject) => {
            var url = document.getElementById("auth-token-url").value;
            fetch(url, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    'fingerprint': fingerprint
                })
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then((data) => {
                    resolve(data.token);
                })
                .catch((error) => {
                    reject(event.target.error); // Reject if there's an error
                });
        });
    }
}

export async function getUserToken() {
    return new Promise((resolve, reject) => {
        var request1 = indexedDB.open(dbName, dbVersion);
        request1.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction("authentications", "readonly");
            const store = transaction.objectStore("authentications");
            var request2 = store.get(fingerprint);
            request2.onsuccess = function () {
                resolve(request2.result ? request2.result.token : null); // Return token
            };
            request2.onerror = function (event) {
                reject("Failed to get token");
            };
        };
    });
}

function updateData(id, updatedData) {
    const transaction = db.transaction("authentications", "readwrite"); // Start a read-write transaction
    const store = transaction.objectStore("authentications"); // Get the "authentications" store
    const getRequest = store.get(id);
    getRequest.onsuccess = function (event) {
        const record = event.target.result;
        if (record) {
            record.token = updatedData;
            const updateRequest = store.put(record);
            updateRequest.onsuccess = function () {
                console.log('Record updated successfully');
            };

            updateRequest.onerror = function () {
                console.log('Error updating record');
            };
        } else {
            console.log('Record not found');
        }
    };
}

function getFingerPrint() {
    const fingerprintData =
        `${navigator.userAgent}|${window.screen.width}x${window.screen.height}|${Intl.DateTimeFormat().resolvedOptions().timeZone}|${navigator.hardwareConcurrency}`;
    return sha256(fingerprintData);
}
