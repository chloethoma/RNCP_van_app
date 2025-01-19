import { LngLatLike } from "mapbox-gl"

export interface FeatureCollection {
    type: "FeatureCollection",
    features: Feature[]
}

export interface Feature {
    type: "Feature",
    geometry: Geometry,
    properties: Properties
}

export interface Geometry {
    type: string,
    coordinates: LngLatLike
}

export interface Properties {
    id: number
}