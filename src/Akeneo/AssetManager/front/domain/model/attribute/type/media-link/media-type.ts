export type NormalizedMediaType = string;
export type MediaType = MediaTypes.image | MediaTypes.pdf | MediaTypes.youtube | MediaTypes.vimeo | MediaTypes.other;

export enum MediaTypes {
  image = 'image',
  pdf = 'pdf',
  youtube = 'youtube',
  vimeo = 'vimeo',
  other = 'other',
}

const validMediaTypes = Object.values(MediaTypes);

export const isValidMediaType = (mediaType: NormalizedMediaType): mediaType is MediaType => {
  return validMediaTypes.includes(mediaType as MediaTypes);
};

export const createMediaTypeFromNormalized = (mediaType: NormalizedMediaType): MediaType => {
  if (!isValidMediaType(mediaType)) {
    throw new Error(`MediaType should be ${validMediaTypes.join(',')}`);
  }

  return mediaType;
};

export const createMediaTypeFromString = (mediaType: string): MediaType => {
  return createMediaTypeFromNormalized(mediaType);
};

export const normalizeMediaType = (mediaType: MediaType): NormalizedMediaType => {
  return mediaType;
};
