/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_cpenabled_dcfcwise', {
    product_cpenabled_dcfc_id: {
      type: DataTypes.INTEGER(15),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    cp_enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    is_sellable: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    esu: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    elp: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    esp: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    ptr: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    last_po_date: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'product_cpenabled_dcfcwise'
  });
};
