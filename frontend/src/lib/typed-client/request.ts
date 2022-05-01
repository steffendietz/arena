interface SuccessfulResponse<Data extends Record<string, any>> {
    ok: true;
    data: Data;
}

interface ErrorResponse {
    ok: false;
    data: undefined;
}

type RequestResponse<Data extends Record<string, any>> =
    | SuccessfulResponse<Data>
    | ErrorResponse;

export const request = async <Data extends Record<string, any>>(
    url: string,
    options: RequestInit
): Promise<RequestResponse<Data>> => {
    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error(response.statusText)
        }

        const data = await response.json();
        return {
            ok: true,
            data
        };
    } catch (e) {
        return {
            ok: false,
            data: undefined
        }
    }
};