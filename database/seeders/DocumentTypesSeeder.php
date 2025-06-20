<?php

namespace Database\Seeders;

use App\Models\document_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $document_type = new document_type();
        $document_type->name ='Business Plan';
        $document_type->acronym ='BP';
        $document_type->description = 'A detailed document outlining a companys goals, strategies for achieving them, market analysis, financial projections, and operational structure. It serves as a roadmap for starting or managing a business and is often used to attract investors or secure loans.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Request for Name Search and Name Reservation';
        $document_type->acronym ='RN';
        $document_type->description = 'It is a document that reflects the process undertaken with the relevant regulatory authority in any jurisdiction to secure the name of a new business entity. This procedure ensures that the proposed name is unique and complies with local regulations, facilitating the formal establishment of the company.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Articles of Incorporation';
        $document_type->acronym ='AI';
        $document_type->description = "Is a legal document filed with a governmental authority to formally establish a corporation. This document outlines essential information about the company, including its name, purpose, structure, and the number of shares authorized for issuance. It serves as the foundation for the corporation's legal existence.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Notice of Directors';
        $document_type->acronym ='ND';
        $document_type->description = "is a formal document that provides information about the individuals appointed to serve on a corporation's board of directors. This notice typically includes the names, addresses, and positions of the directors, and is often required to be filed with the relevant regulatory authority to ensure compliance with corporate governance regulations.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Notice of Address';
        $document_type->acronym ='NA';
        $document_type->description = "Is a formal document that specifies the official address of a corporation or business entity. This notice is often required by regulatory authorities to ensure that the company has a designated location for receiving legal correspondence and official communications. It helps maintain transparency and facilitates proper communication with stakeholders.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Letter of Permission to Establish';
        $document_type->acronym ='PS';
        $document_type->description = "Is a formal document issued by a regulatory authority granting approval for a business or organization to be established in a specific jurisdiction. This letter typically outlines the conditions under which the entity may operate and confirms that it has met the necessary legal and regulatory requirements to commence operations.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Certificate of Registration';
        $document_type->acronym ='CR';
        $document_type->description = 'A document that confirms a business or entity is officially registered with a government authority. This could be for a company, trademark, or other entities requiring legal recognition.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Certificate of Incorporation';
        $document_type->acronym ='CI';
        $document_type->description = 'A legal document issued by a government authority that formally establishes a company as a corporation. It includes essential information such as the companys name, type of corporation, and date of incorporation.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Certificate of Amendment';
        $document_type->acronym ='CA';
        $document_type->description = 'A document filed with a government authority to officially change or amend the details of a company’s original Certificate of Incorporation. Amendments might include changes to the company’s name, purpose, or structure.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='License';
        $document_type->acronym ='LE';
        $document_type->description = "Is an official authorization granted by a regulatory authority that permits an individual or business to engage in specific activities or operations. Licenses are typically required for various professions, industries, or services, ensuring that the licensee complies with legal, safety, and regulatory standards. This document serves as proof that the entity is qualified and legally allowed to conduct its operations.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Services Agreement';
        $document_type->acronym ='SA';
        $document_type->description = 'A contract between a service provider and a client that outlines the scope of services to be provided, the terms of delivery, compensation, and other key details to ensure both parties understand their obligations and expectations.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Amendment to a Services Agreement';
        $document_type->acronym ='AS';
        $document_type->description = "Is a formal document that modifies specific terms and conditions of an existing services contract between parties. This amendment may address changes such as scope of services, pricing, timelines, or other relevant provisions. It ensures that both parties agree to the new terms while maintaining the validity of the original agreement.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Organizational Resolutions';
        $document_type->acronym ='OR';
        $document_type->description = 'Formal decisions or actions taken by a corporation’s board of directors or shareholders. These resolutions can cover various matters such as approving financial transactions, appointing officers, or adopting policies.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Share Certificate';
        $document_type->acronym ='SC';
        $document_type->description = "Is an official document that represents ownership of a specific number of shares in a corporation. This certificate includes details such as the shareholder's name, the number of shares owned, and the company's information. It serves as proof of ownership and may be required for transferring shares or exercising shareholder rights.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='By-Laws';
        $document_type->acronym ='BL';
        $document_type->description = "a set of rules and regulations that govern the internal management and operations of a corporation or organization. They outline the structure of the organization, including the roles and responsibilities of directors and officers, procedures for meetings, and guidelines for decision-making. By-laws provide clarity and consistency in governance, ensuring that the organization operates in accordance with its objectives and legal requirements.";
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Letter of Dissolution FSC';
        $document_type->acronym ='LD';
        $document_type->description = 'A formal letter used to notify the relevant authorities or stakeholders about the dissolution of a company. FSC typically stands for Financial Services Commission or similar regulatory body, depending on the jurisdiction.';
        $document_type->save();

        $document_type = new document_type();
        $document_type->name ='Certificate of Dissolution';
        $document_type->acronym ='CD';
        $document_type->description = 'A legal document issued when a company is officially dissolved or terminated. It signifies that the business has completed its winding-up process and is no longer active.';
        $document_type->save();

    }
}
