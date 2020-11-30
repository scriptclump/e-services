/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('asset', {
    assetid: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    assetcategoryid: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    statusid: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    agencyid: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    model: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    serialno: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    employee: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    invoiceno: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    invoicedate: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    invoiceamount: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    comments: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    hostname: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    office_index: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    location_desc: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    rentalinfo: {
      type: DataTypes.STRING(10),
      allowNull: false,
      defaultValue: 'No'
    },
    rentalreference: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    rentalstartdate: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    rentalenddate: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    rentalvalue: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    assetidentifier: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    ipaddress: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    auditstatus: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    assigned_type: {
      type: DataTypes.STRING(50),
      allowNull: true,
      defaultValue: 'employee'
    },
    assigned_agency: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    assigned_bg: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    parent_assetid: {
      type: DataTypes.STRING(50),
      allowNull: true,
      defaultValue: '0'
    },
    paymentref: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    po_date: {
      type: DataTypes.STRING(30),
      allowNull: true,
      defaultValue: ''
    },
    po_num: {
      type: DataTypes.STRING(30),
      allowNull: true,
      defaultValue: ''
    }
  }, {
    tableName: 'asset'
  });
};
