# VANSCAPE API GUIDELINES

## Table of Contents

- [Introduction](#introduction)
- [Guidelines](#guidelines)
  - [Architecture](#architecture)
  - [Principles](#principles)
    - [Separation of Concerns](#separation-of-concerns)
    - [Single Responsibility](#single-responsibility)
  - [Layers and Responsibilities](#layers-and-responsibilities)
    - [Controllers](#controllers)
    - [Handlers](#handlers)
    - [Data Transfer Objects (DTO)](#data-transfer-objects-dto)
    - [Data Transformers](#data-transformers)
    - [Entity](#entity)
    - [Managers](#managers)
    - [Repositories](#repositories)
  - [Summary](#summary)
  - [Execution Flow Example](#execution-flow-example)
  - [Folder Architecture](#folder-architecture)
  - [Testing](#testing)

## Introduction

This API is configured using a Docker-based setup for the Symfony framework, powered by FrankenPHP and Caddy.  All setup instructions are available [here](docs/symfony-docker.md).

For more informations : [Docker base setup for Symfony (dunglas)](https://github.com/dunglas/symfony-docker)  

## Guidelines

This document outlines best practices and guidelines for structuring the code in this project to ensure a clean, maintainable, and scalable architecture. These guidelines leverage patterns like DTOs, Mappers, and Handlers to achieve separation of concerns.


### Architecture
This structure follows a layered architecture, inspired by Clean Architecture and Hexagonal principles.  
It aims to keep domain logic isolated from infrastructure concerns while promoting modularity and testability.

Here is a schematic representation of the architecture:

![Architecture](docs/architecture.jpg)

---

### Principles

#### Separation of Concerns
Each layer of the application should focus on a single responsibility:

- Presentation Layer: 
  * Controllers: Handle HTTP requests and delegate tasks to the application layer.
- Application Layer:
  * Handlers: Encapsulate use case logic.
  * DTOs: Handle data structures for input/output.
  * Data Transformers: Transform data between DTOs and Entities.
- Domain Layer:
  * Entities: Represent the core business model and enforce business rules.
  * Managers: Encapsulate business logic for a specific domain.
- Infrastructure Layer:
  * Repositories: Handle data persistence and interactions with database (Doctrine).

#### Single Responsibility
Each component (class, method, or function) should have a single reason to change. Avoid mixing responsibilities like validation, mapping, or persistence in one place.

---

### Layers and responsibilities

#### Controllers

Controllers handle HTTP requests and delegate the logic to handlers. Input Validation errors will be handled automatically if you use `MapRequestPayload` or `MapQueryParameter` feature of Symfony.

Guidelines :

* Controller SHOULD extends from `ApiController`.
* Controller SHOULD use `MapRequestPayload` OR `MapQueryParameter` with DTO, and use validationGroups and serializationContext to handle different cases (create, update...).
* Controller MUST use Handler to delegate use case logic.
* Controller MUST catch exceptions and serve corresponding response.
* Controller MUST use DTO with serialization groups to serve data.

#### Handlers
Handlers encapsulate use case-specific business logic. They are responsible for:

* Handling the input data.
* Returning the output data.
* Handlers SHOULD delegate business logic to managers when complexity justifies it. For simple use cases, logic may remain in the handler.

Guidelines:

* Handlers SHOULD be named after the use case they handle.
* Handlers MUST use dataTransformers to convert DTOs to Entities and vice versa.
* Handlers MUST use repositories to handle data access and persistence.
* Handlers MUST delegate the logic to managers.
* Handlers MUST return DTOs.

#### Data Transfer Objects (DTO)

DTOs handle structured data for communication between users and application layer. They should include the minimum required fields for the operation.

Guidelines:

* Properties MUST be `readonly` and `public` to ensure immutability.
* Properties MUST be declared inside of the constructor.
* DTOs SHOULD NOT contain any business logic or methods.
* Validation constraints SHOULD be used to enforce data integrity.
* Validation constraints SHOULD be used with groups to separate validation rules for different scenarios like create, update.
* DTOs SHOULD use serializer groups to control which properties are serialized in different scenarios.
* All group names SHOULD be descriptive and match the use case (`create`, `read`, `update`).

#### Data Transformers

DataTransformers convert data between DTOs and Entities. This ensures a clear separation of concerns and keeps mapping logic reusable.

Guidelines:

* Use `setEntity`and `setDTO` methods to set the Entity and DTOs.
* Use `Validator` class to validate data when Entities are map in DTOs.

#### Entity
Entities are the core business models. Every business rule should be written using Entities (like Spot, User).

Guidelines :

* Entities SHOULD represent the models in the way that is the best for business rules manipulation and can be significantly different with DTOs.

> Collection of entities can also be implemented. This is useful  for type hinting and storing specific filtering or sorting logic functions. If you need it make sure your collection extends form `ArrayObject`.

#### Managers

Managers encapsulate the business logic for a specific domain.
They use Entities to enforce business rules and handle complex operations.

Guidelines :
* Managers MUST only contain business logic.
* Managers MUST use Entities to enforce business rules.
* Managers SHOULD be named after the domain they handle.
* Managers SHOULD be introduced only when business logic becomes complex or shared across multiple handlers.

#### Repositories

Repositories handle data access and persistence. They abstract the underlying data storage mechanism, providing a clean API for the rest of the application to interact with the data layer.

Guidelines:

* Repositories SHOULD encapsulate all data access logic.
* Repositories SHOULD use Registry to communicate with database.
* Repositories SHOULD provide methods for common data operations like `find`, `create`, `update`, and `delete` or more complex operations.
* Repositories SHOULD return domain entities (Doctrine entities) ready to be used in the application.

---

### Summary

| Component | Responsibility |
| --------- | --------- |
| Controller | Handles HTTP requests and delegates logic. |
| Handler | Contains use case-specific business logic. |
| DTO | Validates and Structures user data. |
| Data Transformer | Transforms data between DTOs and Entities. |
| Entity | Represent the core data models. |
| Manager | Encapsulates business logic for a specific domain. |
| Repositories | Handles data access and persistence. |

Adhering to these guidelines ensures a clean, modular, and testable API architecture.

---

### Execution Flow example

Here is a typical flow of data and logic :

```text
HTTP Request
     |
     v
Controller
  ├─ Parses input with `MapRequestPayload` or `MapQueryParameter`
  ├─ Validates with DTO + groups
  └─ Delegates to Handler
     |
     v
Handler
  ├─ Transforms DTO to Entity --> DataTransformer
  ├─ Delegates to Manager for business logic (if needed) --> Manager
  ├─ Interact / persists with database (via Doctrine) --> Repository
  └─ Transforms Entity to DTO --> DataTransformer
     |
     v
HTTP Response (JSON)
```

---

### Folder Architecture

```
+ src
  |   + Controller
  |   + DataTransformer
  |   + DTO
  |   + Entity
  |   + EventListener
  |   + EventSubscriber
  |   + Handler
  |   + Repository
  |   + Exception
  |   + Validator
  |   + Manager
```

---

### Testing

Unit and integration tests SHOULD be written for handlers, managers, and critical transformers.  
A detailed testing strategy will be documented soon.

