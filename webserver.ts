import {getNetworkAddr} from 'https://deno.land/x/local_ip/mod.ts';
import {format} from "https://deno.land/std@0.91.0/datetime/mod.ts";
import {readLines} from "https://deno.land/std/io/bufio.ts";
// Start listening on port 8080 of localhost.
const server = Deno.listen({port: 8080});
console.log(`HTTP webserver running.  Access it at:  http://localhost:8080/`);

// Connections to the server will be yielded up as an async iterable.
for await (const conn of server) {
    // In order to not be blocking, we need to handle each connection individually
    // without awaiting the function
    serveHttp(conn);
}

async function serveHttp(conn: Deno.Conn) {
    // This "upgrades" a network connection into an HTTP connection.
    const httpConn = Deno.serveHttp(conn);
    const netAddr = await getNetworkAddr();
    // Each request sent over the HTTP connection will be yielded as an async
    // iterator from the HTTP connection.
    for await (const requestEvent of httpConn) {
        // The native HTTP server uses the web standard `Request` and `Response`
        // objects.

        const appendingFile = await Deno.open("./append", {create: true, append: true});

        const client = requestEvent.request.headers.get(
            "host",
        ) ?? "Unknown"

        const logEntry = `${format(new Date(), "yyyy-MM-dd HH:mm:ss")} - Client : ${client} | Server : ${netAddr}\n`

        await appendingFile.write(new TextEncoder().encode(logEntry))

        const appendingFileRead = await Deno.open("./append");

        let logData = ''

        for await(const l of readLines(appendingFileRead)) {
            logData = `${l}<br>${logData}`;
        }

        const body = `
        <b>Current</b>: ${logEntry}<br><br>
        <b>Log</b><br>
        ${logData}
        `;
        const bodyHTML = new TextEncoder().encode(body);
        // The requestEvent's `.respondWith()` method is how we send the response
        // back to the client.
        requestEvent.respondWith(
            new Response(bodyHTML, {
                status: 200,
            }),
        );
    }
}
