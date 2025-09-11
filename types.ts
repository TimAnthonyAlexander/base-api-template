// Generated TypeScript definitions for BaseApi
// Do not edit manually - regenerate with: php bin/console types:generate

export type UUID = string;
export type Envelope<T> = { data: T };

export interface ErrorResponse {
  error: string;
  requestId: string;
  errors?: Record<string, string>;
}

export interface GetHealthRequestQuery {
  db: string;
  request?: any;
}

export type GetHealthResponse = Envelope<any>;

export interface PostHealthRequestBody {
  db: string;
  request?: any;
}

export type PostHealthResponse = Envelope<any>;

export interface PostSignupRequestBody {
  name: string;
  email: string;
  password: string;
  request?: any;
}

export type PostSignupResponse = Envelope<{ user: any[] }>;

export interface PostLoginRequestBody {
  email: string;
  password: string;
  request?: any;
}

export type PostLoginResponse = Envelope<{ user: any[] }>;

export interface PostLogoutRequestBody {
  request?: any;
}

export type PostLogoutResponse = Envelope<{ message: string }>;

export interface GetMeRequestQuery {
  request?: any;
}

export type GetMeResponse = Envelope<{ user: any[] }>;
