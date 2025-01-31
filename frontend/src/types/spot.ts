export interface Spot {
    id: number,
    latitude: number,
    longitude: number,
    description: string | null,
    isFavorite: boolean,
    ownerId: number
}