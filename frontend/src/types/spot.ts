import { LngLatLike } from "mapbox-gl";
import { User } from "./user";

export interface SpoGeoJsonCollection {
  type: "FeatureCollection";
  features: SpotGeoJson[];
}

export interface SpotGeoJson {
  type: "Feature";
  geometry: Geometry;
  properties: Properties;
}

export interface Geometry {
  type: string;
  coordinates: LngLatLike;
}

export interface Properties {
  spotId: number;
  ownerId: number;
}

export type Owner = Pick<User, "id" | "pseudo" | "picture">;

export interface Spot {
  id: number;
  latitude: number;
  longitude: number;
  description: string;
  isFavorite: boolean;
  owner: Owner;
  picture?: string | null;
}
