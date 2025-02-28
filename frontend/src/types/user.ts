export interface User {
    id: number,
    email: string,
    emailVerified: boolean,
    pseudo: string,
    createdAt: string,
    updatedAt: string,
    picture: string | null,
    token: string | null
    password?: string 
}